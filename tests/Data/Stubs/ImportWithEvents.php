<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Concerns\Importable;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Events\AfterImport;
use VioletWaves\Excel\Events\AfterSheet;
use VioletWaves\Excel\Events\BeforeImport;
use VioletWaves\Excel\Events\BeforeSheet;

class ImportWithEvents implements WithEvents
{
    use Importable;

    /**
     * @var callable
     */
    public $beforeImport;

    /**
     * @var callable
     */
    public $afterImport;

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
            BeforeImport::class => $this->beforeImport ?? function () {
            },
            AfterImport::class => $this->afterImport ?? function () {
            },
            BeforeSheet::class => $this->beforeSheet ?? function () {
            },
            AfterSheet::class => $this->afterSheet ?? function () {
            },
        ];
    }
}
