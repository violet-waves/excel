<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use VioletWaves\Excel\Concerns\Importable;
use VioletWaves\Excel\Concerns\ToModel;
use VioletWaves\Excel\Concerns\WithChunkReading;
use VioletWaves\Excel\Tests\Data\Stubs\Database\Group;

class QueuedImportWithMiddleware implements ShouldQueue, ToModel, WithChunkReading
{
    use Importable;

    /**
     * @param  array  $row
     * @return Model|null
     */
    public function model(array $row)
    {
        return new Group([
            'name' => $row[0],
        ]);
    }

    public function middleware()
    {
        return [function () {
            throw new \Exception('Job reached middleware method');
        }];
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
