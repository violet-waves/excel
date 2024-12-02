<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Concerns\ToModel;
use VioletWaves\Excel\Concerns\WithBatchInserts;
use VioletWaves\Excel\Concerns\WithChunkReading;
use VioletWaves\Excel\Events\AfterBatch;
use VioletWaves\Excel\Events\AfterChunk;

class ImportWithEventsChunksAndBatches extends ImportWithEvents implements WithBatchInserts, ToModel, WithChunkReading
{
    /**
     * @var callable
     */
    public $afterBatch;

    /**
     * @var callable
     */
    public $afterChunk;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return parent::registerEvents() + [
            AfterBatch::class => $this->afterBatch ?? function () {
            },
            AfterChunk::class => $this->afterChunk ?? function () {
            },
        ];
    }

    public function model(array $row)
    {
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
