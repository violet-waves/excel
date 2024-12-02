<?php

namespace VioletWaves\Excel\Tests\Concerns;

use VioletWaves\Excel\Concerns\Importable;
use VioletWaves\Excel\Concerns\RemembersRowNumber;
use VioletWaves\Excel\Concerns\ToModel;
use VioletWaves\Excel\Concerns\WithBatchInserts;
use VioletWaves\Excel\Concerns\WithChunkReading;
use VioletWaves\Excel\Tests\TestCase;

class RemembersRowNumberTest extends TestCase
{
    public function test_can_set_and_get_row_number()
    {
        $import = new class
        {
            use Importable;
            use RemembersRowNumber;
        };

        $import->rememberRowNumber(50);

        $this->assertEquals(50, $import->getRowNumber());
    }

    public function test_can_access_row_number_on_import_to_model()
    {
        $import = new class implements ToModel
        {
            use Importable;
            use RemembersRowNumber;

            public $rowNumbers = [];

            public function model(array $row)
            {
                $this->rowNumbers[] = $this->getRowNumber();
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertEquals([46, 47, 48, 49, 50, 51, 52, 53, 54, 55], array_slice($import->rowNumbers, 45, 10));
    }

    public function test_can_access_row_number_on_import_to_array_in_chunks()
    {
        $import = new class implements ToModel, WithChunkReading
        {
            use Importable;
            use RemembersRowNumber;

            public $rowNumbers = [];

            public function chunkSize(): int
            {
                return 50;
            }

            public function model(array $row)
            {
                $this->rowNumbers[] = $this->getRowNumber();
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertEquals([46, 47, 48, 49, 50, 51, 52, 53, 54, 55], array_slice($import->rowNumbers, 45, 10));
    }

    public function test_can_access_row_number_on_import_to_array_in_chunks_with_batch_inserts()
    {
        $import = new class implements ToModel, WithChunkReading, WithBatchInserts
        {
            use Importable;
            use RemembersRowNumber;

            public $rowNumbers = [];

            public function chunkSize(): int
            {
                return 50;
            }

            public function model(array $row)
            {
                $this->rowNumbers[] = $this->rowNumber;
            }

            public function batchSize(): int
            {
                return 50;
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertEquals([46, 47, 48, 49, 50, 51, 52, 53, 54, 55], array_slice($import->rowNumbers, 45, 10));
    }
}
