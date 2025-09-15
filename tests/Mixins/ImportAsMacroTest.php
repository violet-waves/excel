<?php

namespace VioletWaves\Excel\Tests\Mixins;

use VioletWaves\Excel\Tests\Data\Stubs\Database\User;
use VioletWaves\Excel\Tests\TestCase;

class ImportAsMacroTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    public function test_can_import_directly_into_a_model_with_mapping()
    {
        User::query()->truncate();

        User::importAs('import-users.xlsx', function (array $row) {
            return [
                'name'     => $row[0],
                'email'    => $row[1],
                'password' => 'secret',
            ];
        });

        $this->assertCount(2, User::all());
        $this->assertEquals([
            'meet@violetwaves.in',
            'taylor@laravel.com',
        ], User::query()->pluck('email')->all());
    }
}
