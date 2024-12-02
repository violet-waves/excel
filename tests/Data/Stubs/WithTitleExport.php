<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\WithTitle;

class WithTitleExport implements WithTitle
{
    use Exportable;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'given-title';
    }
}
