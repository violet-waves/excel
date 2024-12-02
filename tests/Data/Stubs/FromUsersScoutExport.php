<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Laravel\Scout\Builder as ScoutBuilder;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\FromQuery;
use VioletWaves\Excel\Concerns\WithCustomChunkSize;
use VioletWaves\Excel\Tests\Data\Stubs\Database\User;

class FromUsersScoutExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder|EloquentBuilder|Relation|ScoutBuilder
     */
    public function query()
    {
        return new ScoutBuilder(new User, '');
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
