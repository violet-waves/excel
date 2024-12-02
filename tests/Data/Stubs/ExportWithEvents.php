<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Events\AfterSheet;
use VioletWaves\Excel\Events\BeforeExport;
use VioletWaves\Excel\Events\BeforeSheet;
use VioletWaves\Excel\Events\BeforeWriting;

class ExportWithEvents implements WithEvents
{
    use Exportable;

    /**
     * @var callable
     */
    public $beforeExport;

    /**
     * @var callable
     */
    public $beforeWriting;

    /**
     * @var callable
     */
    public $beforeSheet;

    /**
     * @var callable
     */
    public $afterSheet;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class  => $this->beforeExport ?? function () {
            },
            BeforeWriting::class => $this->beforeWriting ?? function () {
            },
            BeforeSheet::class   => $this->beforeSheet ?? function () {
            },
            AfterSheet::class    => $this->afterSheet ?? function () {
            },
        ];
    }
}
