<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Database\Eloquent\Collection;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\FromCollection;
use VioletWaves\Excel\Concerns\WithMapping;
use VioletWaves\Excel\Tests\Data\Stubs\Database\User;

class EloquentCollectionWithMappingExport implements FromCollection, WithMapping
{
    use Exportable;

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            new User([
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ]),
        ]);
    }

    /**
     * @param  User  $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->firstname,
            $user->lastname,
        ];
    }
}
