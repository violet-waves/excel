<?php

namespace VioletWaves\Excel\Tests\Concerns;

use Illuminate\Support\Collection;
use VioletWaves\Excel\Concerns\FromCollection;
use VioletWaves\Excel\Concerns\WithCustomStartCell;
use VioletWaves\Excel\Excel;
use VioletWaves\Excel\Tests\TestCase;

class WithCustomStartCellTest extends TestCase
{
    /**
     * @var Excel
     */
    protected $SUT;

    protected function setUp(): void
    {
        parent::setUp();

        $this->SUT = $this->app->make(Excel::class);
    }

    public function test_can_store_collection_with_custom_start_cell()
    {
        $export = new class implements FromCollection, WithCustomStartCell
        {
            /**
             * @return Collection
             */
            public function collection()
            {
                return collect([
                    ['A1', 'B1'],
                    ['A2', 'B2'],
                ]);
            }

            /**
             * @return string
             */
            public function startCell(): string
            {
                return 'B2';
            }
        };

        $this->SUT->store($export, 'custom-start-cell.csv');

        $contents = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/custom-start-cell.csv', 'Csv');

        $this->assertEquals([
            [null, null, null],
            [null, 'A1', 'B1'],
            [null, 'A2', 'B2'],
        ], $contents);
    }
}
