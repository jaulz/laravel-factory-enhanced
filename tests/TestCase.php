<?php

namespace Jaulz\LaravelFactory\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Jaulz\LaravelFactory\Factory;
use Jaulz\LaravelFactory\FactoryBuilder;
use Jaulz\LaravelFactory\FactoryServiceProvider;
use Jaulz\LaravelFactory\StateManager;
use Jaulz\LaravelFactory\Tests\Stubs\User;

class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(FactoryServiceProvider::class);
        $app->useDatabasePath(__DIR__.'/database');
//        $app->afterResolving('migrator', function ($migrator) {
//            $migrator->path(__DIR__.'/migrations/');
//        });

        $app['config']->set('database.default', 'primary');
        $app['config']->set('database.connections.primary', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('database.connections.secondary', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->singleton(StateManager::class);

        // Make tests faster!
        Hash::setRounds(4);

        return $app;
    }

    /**
     * @param null $class
     * @return Factory | FactoryBuilder
     */
    protected function factory($class = null)
    {
        $factory = app(Factory::class);

        if ($class) {
            return $factory->of($class);
        }

        return $factory;
    }

    /**
     * @return User
     */
    protected function user()
    {
        return factory(User::class)->create();
    }
}
