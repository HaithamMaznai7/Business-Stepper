<?php

namespace haimaz\BusinessSteper\Tests;

use haimaz\BusinessSteper\BusinessSteperProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [BusinessSteperProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configure the "testing" database connection
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:', // in-memory database
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Optionally, load Laravel’s default migrations
        $this->loadLaravelMigrations();

        // Run migrations
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }
}