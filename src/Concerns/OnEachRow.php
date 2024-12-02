<?php

namespace VioletWaves\Excel\Concerns;

use VioletWaves\Excel\Row;

interface OnEachRow
{
    /**
     * @param  Row  $row
     */
    public function onRow(Row $row);
}
