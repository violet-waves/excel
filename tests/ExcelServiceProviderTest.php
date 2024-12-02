<?php

namespace VioletWaves\Excel\Tests;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Illuminate\Contracts\Console\Kernel;
use VioletWaves\Excel\Cache\MemoryCache;
use VioletWaves\Excel\Cache\MemoryCacheDeprecated;
use VioletWaves\Excel\Excel;
use VioletWaves\Excel\Tests\Data\Stubs\CustomTransactionHandler;
use VioletWaves\Excel\Transactions\TransactionManager;
use PhpOffice\PhpSpreadsheet\Settings;

class ExcelServiceProviderTest extends TestCase
{
    public function test_custom_transaction_handler_is_bound()
    {
        $this->app->make(TransactionManager::class)->extend('handler', function () {
            return new CustomTransactionHandler;
        });

        $this->assertInstanceOf(CustomTransactionHandler::class, $this->app->make(TransactionManager::class)->driver('handler'));
    }

    public function test_is_bound()
    {
        $this->assertTrue($this->app->bound('excel'));
    }

    public function test_has_aliased()
    {
        $this->assertTrue($this->app->isAlias(Excel::class));
        $this->assertEquals('excel', $this->app->getAlias(Excel::class));
    }

    public function test_registers_console_commands()
    {
        /** @var Kernel $kernel */
        $kernel   = $this->app->make(Kernel::class);
        $commands = $kernel->all();

        $this->assertArrayHasKey('make:export', $commands);
        $this->assertArrayHasKey('make:import', $commands);
    }

    public function test_sets_php_spreadsheet_settings()
    {
        $driver = config('excel.cache.driver');

        $this->assertEquals('memory', $driver);

        if (InstalledVersions::satisfies(new VersionParser, 'psr/simple-cache', '^3.0')) {
            $this->assertInstanceOf(
                MemoryCache::class,
                Settings::getCache()
            );
        } else {
            $this->assertInstanceOf(
                MemoryCacheDeprecated::class,
                Settings::getCache()
            );
        }
    }
}
