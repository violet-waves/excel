<?php

namespace VioletWaves\Excel;

use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use VioletWaves\Excel\Concerns\FromCollection;
use VioletWaves\Excel\Concerns\FromQuery;
use VioletWaves\Excel\Concerns\FromView;
use VioletWaves\Excel\Concerns\WithCustomChunkSize;
use VioletWaves\Excel\Concerns\WithCustomQuerySize;
use VioletWaves\Excel\Concerns\WithMultipleSheets;
use VioletWaves\Excel\Files\TemporaryFile;
use VioletWaves\Excel\Files\TemporaryFileFactory;
use VioletWaves\Excel\Jobs\AppendDataToSheet;
use VioletWaves\Excel\Jobs\AppendPaginatedToSheet;
use VioletWaves\Excel\Jobs\AppendQueryToSheet;
use VioletWaves\Excel\Jobs\AppendViewToSheet;
use VioletWaves\Excel\Jobs\CloseSheet;
use VioletWaves\Excel\Jobs\QueueExport;
use VioletWaves\Excel\Jobs\StoreQueuedExport;
use Traversable;

class QueuedWriter
{
    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @param  Writer  $writer
     * @param  TemporaryFileFactory  $temporaryFileFactory
     */
    public function __construct(Writer $writer, TemporaryFileFactory $temporaryFileFactory)
    {
        $this->writer               = $writer;
        $this->chunkSize            = config('excel.exports.chunk_size', 1000);
        $this->temporaryFileFactory = $temporaryFileFactory;
    }

    /**
     * @param  object  $export
     * @param  string  $filePath
     * @param  string  $disk
     * @param  string|null  $writerType
     * @param  array|string  $diskOptions
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function store($export, string $filePath, ?string $disk = null, ?string $writerType = null, $diskOptions = [])
    {
        $extension     = pathinfo($filePath, PATHINFO_EXTENSION);
        $temporaryFile = $this->temporaryFileFactory->make($extension);

        $jobs = $this->buildExportJobs($export, $temporaryFile, $writerType);

        $jobs->push(new StoreQueuedExport(
            $temporaryFile,
            $filePath,
            $disk,
            $diskOptions
        ));

        return new PendingDispatch(
            (new QueueExport($export, $temporaryFile, $writerType))->chain($jobs->toArray())
        );
    }

    /**
     * @param  object  $export
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @return Collection
     */
    private function buildExportJobs($export, TemporaryFile $temporaryFile, string $writerType): Collection
    {
        $sheetExports = [$export];
        if ($export instanceof WithMultipleSheets) {
            $sheetExports = $export->sheets();
        }

        $jobs = new Collection;
        foreach ($sheetExports as $sheetIndex => $sheetExport) {
            if ($sheetExport instanceof FromCollection) {
                $jobs = $jobs->merge($this->exportCollection($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof FromQuery) {
                $jobs = $jobs->merge($this->exportQuery($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            } elseif ($sheetExport instanceof FromView) {
                $jobs = $jobs->merge($this->exportView($sheetExport, $temporaryFile, $writerType, $sheetIndex));
            }

            $jobs->push(new CloseSheet($sheetExport, $temporaryFile, $writerType, $sheetIndex));
        }

        return $jobs;
    }

    /**
     * @param  FromCollection  $export
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @param  int  $sheetIndex
     * @return Collection|LazyCollection
     */
    private function exportCollection(
        FromCollection $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ) {
        return $export
            ->collection()
            ->chunk($this->getChunkSize($export))
            ->map(function ($rows) use ($writerType, $temporaryFile, $sheetIndex, $export) {
                if ($rows instanceof Traversable) {
                    $rows = iterator_to_array($rows);
                }

                return new AppendDataToSheet(
                    $export,
                    $temporaryFile,
                    $writerType,
                    $sheetIndex,
                    $rows
                );
            });
    }

    /**
     * @param  FromQuery  $export
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @param  int  $sheetIndex
     * @return Collection
     */
    private function exportQuery(
        FromQuery $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        $query = $export->query();

        if ($query instanceof \Laravel\Scout\Builder) {
            return $this->exportScout($export, $temporaryFile, $writerType, $sheetIndex);
        }

        $count = $export instanceof WithCustomQuerySize ? $export->querySize() : $query->count();
        $spins = ceil($count / $this->getChunkSize($export));

        $jobs = new Collection();

        for ($page = 1; $page <= $spins; $page++) {
            $jobs->push(new AppendQueryToSheet(
                $export,
                $temporaryFile,
                $writerType,
                $sheetIndex,
                $page,
                $this->getChunkSize($export)
            ));
        }

        return $jobs;
    }

    /**
     * @param  FromQuery  $export
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @param  int  $sheetIndex
     * @return Collection
     */
    private function exportScout(
        FromQuery $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        $jobs = new Collection();

        $chunk = $export->query()->paginate($this->getChunkSize($export));
        // Append first page
        $jobs->push(new AppendDataToSheet(
            $export,
            $temporaryFile,
            $writerType,
            $sheetIndex,
            $chunk->items()
        ));

        // Append rest of pages
        for ($page = 2; $page <= $chunk->lastPage(); $page++) {
            $jobs->push(new AppendPaginatedToSheet(
                $export,
                $temporaryFile,
                $writerType,
                $sheetIndex,
                $page,
                $this->getChunkSize($export)
            ));
        }

        return $jobs;
    }

    /**
     * @param  FromView  $export
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $writerType
     * @param  int  $sheetIndex
     * @return Collection
     */
    private function exportView(
        FromView $export,
        TemporaryFile $temporaryFile,
        string $writerType,
        int $sheetIndex
    ): Collection {
        $jobs = new Collection();
        $jobs->push(new AppendViewToSheet(
            $export,
            $temporaryFile,
            $writerType,
            $sheetIndex
        ));

        return $jobs;
    }

    /**
     * @param  object|WithCustomChunkSize  $export
     * @return int
     */
    private function getChunkSize($export): int
    {
        if ($export instanceof WithCustomChunkSize) {
            return $export->chunkSize();
        }

        return $this->chunkSize;
    }
}
