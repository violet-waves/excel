<?php

namespace VioletWaves\Excel\Tests\Concerns;

use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\WithMultipleSheets;
use VioletWaves\Excel\Concerns\WithTitle;
use VioletWaves\Excel\Tests\Data\Stubs\WithTitleExport;
use VioletWaves\Excel\Tests\TestCase;

class WithTitleTest extends TestCase
{
    public function test_can_export_with_title()
    {
        $export = new WithTitleExport();

        $response = $export->store('with-title-store.xlsx');

        $this->assertTrue($response);

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/with-title-store.xlsx', 'Xlsx');

        $this->assertEquals('given-title', $spreadsheet->getProperties()->getTitle());
        $this->assertEquals('given-title', $spreadsheet->getActiveSheet()->getTitle());
    }

    public function test_can_export_sheet_title_when_longer_than_max_length()
    {
        $export = new class implements WithTitle, WithMultipleSheets
        {
            use Exportable;

            /**
             * @return string
             */
            public function title(): string
            {
                return '12/3456789123/45678912345/678912345/6789';
            }

            public function sheets(): array
            {
                return [$this];
            }
        };

        $response = $export->store('with-title-store.xlsx');
        $this->assertTrue($response);

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/with-title-store.xlsx', 'Xlsx');

        $this->assertEquals('12/3456789123/45678912345/678912345/6789', $spreadsheet->getProperties()->getTitle());
        $this->assertEquals('1234567891234567891234567891234', $spreadsheet->getActiveSheet()->getTitle());
    }
}
