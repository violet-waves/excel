<?php

namespace VioletWaves\Excel\Exceptions;

use LogicException;

class NoSheetsFoundException extends LogicException implements LaravelExcelException
{
}
