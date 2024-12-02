<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use Illuminate\Support\LazyCollection;
use VioletWaves\Excel\Concerns\Exportable;
use VioletWaves\Excel\Concerns\FromCollection;

class EloquentLazyCollectionExport implements FromCollection
{
    use Exportable;

    public function collection(): LazyCollection
    {
        return collect([
            [
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ],
            [
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ],
            [
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ],
            [
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ],
        ])->lazy();
    }
}
