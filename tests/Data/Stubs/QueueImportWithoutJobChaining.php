<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Concerns\Importable;
use VioletWaves\Excel\Concerns\ShouldQueueWithoutChain;
use VioletWaves\Excel\Concerns\ToModel;
use VioletWaves\Excel\Concerns\WithChunkReading;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Events\AfterImport;
use VioletWaves\Excel\Events\BeforeImport;
use VioletWaves\Excel\Reader;
use VioletWaves\Excel\Tests\Data\Stubs\Database\User;
use PHPUnit\Framework\Assert;

class QueueImportWithoutJobChaining implements ToModel, WithChunkReading, WithEvents, ShouldQueueWithoutChain
{
    use Importable;

    public $queue;
    public $before = false;
    public $after  = false;

    /**
     * @param  array  $row
     * @return Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => 'secret',
        ]);
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                Assert::assertInstanceOf(Reader::class, $event->reader);
                $this->before = true;
            },
            AfterImport::class  => function (AfterImport $event) {
                Assert::assertInstanceOf(Reader::class, $event->reader);
                $this->after = true;
            },
        ];
    }
}
