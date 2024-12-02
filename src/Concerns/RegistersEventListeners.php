<?php

namespace VioletWaves\Excel\Concerns;

use VioletWaves\Excel\Events\AfterBatch;
use VioletWaves\Excel\Events\AfterChunk;
use VioletWaves\Excel\Events\AfterImport;
use VioletWaves\Excel\Events\AfterSheet;
use VioletWaves\Excel\Events\BeforeExport;
use VioletWaves\Excel\Events\BeforeImport;
use VioletWaves\Excel\Events\BeforeSheet;
use VioletWaves\Excel\Events\BeforeWriting;
use VioletWaves\Excel\Events\ImportFailed;

trait RegistersEventListeners
{
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $listenersClasses = [
            BeforeExport::class  => 'beforeExport',
            BeforeWriting::class => 'beforeWriting',
            BeforeImport::class  => 'beforeImport',
            AfterImport::class   => 'afterImport',
            AfterBatch::class    => 'afterBatch',
            AfterChunk::class    => 'afterChunk',
            ImportFailed::class  => 'importFailed',
            BeforeSheet::class   => 'beforeSheet',
            AfterSheet::class    => 'afterSheet',
        ];
        $listeners = [];

        foreach ($listenersClasses as $class => $name) {
            // Method names are case insensitive in php
            if (method_exists($this, $name)) {
                // Allow methods to not be static
                $listeners[$class] = [$this, $name];
            }
        }

        return $listeners;
    }
}
