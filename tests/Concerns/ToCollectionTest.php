<?php

namespace VioletWaves\Excel\Tests\Concerns;

use Illuminate\Support\Collection;
use VioletWaves\Excel\Concerns\Importable;
use VioletWaves\Excel\Concerns\ToCollection;
use VioletWaves\Excel\Tests\TestCase;
use PHPUnit\Framework\Assert;

class ToCollectionTest extends TestCase
{
    public function test_can_import_to_collection()
    {
        $import = new class implements ToCollection
        {
            use Importable;

            public $called = false;

            /**
             * @param  Collection  $collection
             */
            public function collection(Collection $collection)
            {
                $this->called = true;

                Assert::assertEquals([
                    ['test', 'test'],
                    ['test', 'test'],
                ], $collection->toArray());
            }
        };

        $import->import('import.xlsx');

        $this->assertTrue($import->called);
    }

    public function test_can_import_multiple_sheets_to_collection()
    {
        $import = new class implements ToCollection
        {
            use Importable;

            public $called = 0;

            /**
             * @param  Collection  $collection
             */
            public function collection(Collection $collection)
            {
                $this->called++;

                $sheetNumber = $this->called;

                Assert::assertEquals([
                    [$sheetNumber . '.A1', $sheetNumber . '.B1'],
                    [$sheetNumber . '.A2', $sheetNumber . '.B2'],
                ], $collection->toArray());
            }
        };

        $import->import('import-multiple-sheets.xlsx');

        $this->assertEquals(2, $import->called);
    }
}
