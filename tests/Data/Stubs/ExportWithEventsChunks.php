<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\FromQuery;
use VioletWaves\Excel\Concerns\WithCustomChunkSize;
use VioletWaves\Excel\Concerns\WithEvents;
use VioletWaves\Excel\Events\AfterChunk;
use VioletWaves\Excel\Tests\Data\Stubs\Database\User;
use PHPUnit\Framework\Assert;

class ExportWithEventsChunks implements WithEvents, FromQuery, ShouldQueue, WithCustomChunkSize
{
    use Exportable;

    public static $calledEvent = 0;

    public function registerEvents(): array
    {
        return [
            AfterChunk::class => function (AfterChunk $event) {
                ExportWithEventsChunks::$calledEvent++;
                Assert::assertInstanceOf(ExportWithEventsChunks::class, $event->getConcernable());
            },
        ];
    }

    public function query(): Builder
    {
        return User::query();
    }

    public function chunkSize(): int
    {
        return 1;
    }
}
