<?php

namespace VioletWaves\Excel;

use Illuminate\Support\Collection;
use VioletWaves\Excel\Concerns\ToArray;
use VioletWaves\Excel\Concerns\ToCollection;
use VioletWaves\Excel\Concerns\ToModel;
use VioletWaves\Excel\Concerns\WithCalculatedFormulas;
use VioletWaves\Excel\Concerns\WithFormatData;
use VioletWaves\Excel\Concerns\WithMappedCells;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MappedReader
{
    /**
     * @param  WithMappedCells  $import
     * @param  Worksheet  $worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function map(WithMappedCells $import, Worksheet $worksheet)
    {
        $mapped = $import->mapping();
        array_walk_recursive($mapped, function (&$coordinate) use ($import, $worksheet) {
            $cell = Cell::make($worksheet, $coordinate);

            $coordinate = $cell->getValue(
                null,
                $import instanceof WithCalculatedFormulas,
                $import instanceof WithFormatData
            );
        });

        if ($import instanceof ToModel) {
            $model = $import->model($mapped);

            if ($model) {
                $model->saveOrFail();
            }
        }

        if ($import instanceof ToCollection) {
            $import->collection(new Collection($mapped));
        }

        if ($import instanceof ToArray) {
            $import->array($mapped);
        }
    }
}
