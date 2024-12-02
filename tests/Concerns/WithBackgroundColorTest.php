<?php

namespace VioletWaves\Excel\Tests\Concerns;

use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\WithBackgroundColor;
use VioletWaves\Excel\Tests\TestCase;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WithBackgroundColorTest extends TestCase
{
    public function test_can_configure_background_color_from_rgb_string()
    {
        $export = new class implements WithBackgroundColor
        {
            use Exportable;

            public function backgroundColor()
            {
                return '000000';
            }
        };

        $export->store('background-styles.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/background-styles.xlsx', 'Xlsx');
        $sheet       = $spreadsheet->getDefaultStyle();

        $this->assertEquals(Fill::FILL_SOLID, $sheet->getFill()->getFillType());
        $this->assertEquals('000000', $sheet->getFill()->getStartColor()->getRGB());
    }

    public function test_can_configure_background_color_as_array()
    {
        $export = new class implements WithBackgroundColor
        {
            use Exportable;

            public function backgroundColor()
            {
                return [
                    'fillType'   => Fill::FILL_GRADIENT_LINEAR,
                    'startColor' => ['argb' => Color::COLOR_RED],
                ];
            }
        };

        $export->store('background-styles.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/background-styles.xlsx', 'Xlsx');
        $sheet       = $spreadsheet->getDefaultStyle();

        $this->assertEquals(Fill::FILL_GRADIENT_LINEAR, $sheet->getFill()->getFillType());
        $this->assertEquals(Color::COLOR_RED, $sheet->getFill()->getStartColor()->getARGB());
    }

    public function test_can_configure_background_color_with_color_instance()
    {
        $export = new class implements WithBackgroundColor
        {
            use Exportable;

            public function backgroundColor()
            {
                return new Color(Color::COLOR_BLUE);
            }
        };

        $export->store('background-styles.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/background-styles.xlsx', 'Xlsx');
        $sheet       = $spreadsheet->getDefaultStyle();

        $this->assertEquals(Fill::FILL_SOLID, $sheet->getFill()->getFillType());
        $this->assertEquals(Color::COLOR_BLUE, $sheet->getFill()->getStartColor()->getARGB());
    }
}
