<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Exception;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Concerns\WithMultipleSheets;
use VioletWaves\Excel\Events\BeforeExport;
use PHPUnit\Framework\Assert;
use Throwable;

class QueuedExportWithFailedEvents implements WithMultipleSheets, WithEvents
{
    use Exportable;

    /**
     * @return SheetWith100Rows[]
     */
    public function sheets(): array
    {
        return [
            new SheetWith100Rows('A'),
            new SheetWith100Rows('B'),
            new SheetWith100Rows('C'),
        ];
    }

    /**
     * @param  Throwable  $exception
     */
    public function failed(Throwable $exception)
    {
        Assert::assertEquals('catch exception from QueueExport job', $exception->getMessage());

        app()->bind('queue-has-failed-from-queue-export-job', function () {
            return true;
        });
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function () {
                throw new Exception('catch exception from QueueExport job');
            },
        ];
    }
}
