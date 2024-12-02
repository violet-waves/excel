<?php

namespace VioletWaves\Excel\Tests\Data\Stubs;

use VioletWaves\Excel\Transactions\TransactionHandler;

class CustomTransactionHandler implements TransactionHandler
{
    public function __invoke(callable $callback)
    {
        return $callback();
    }
}
