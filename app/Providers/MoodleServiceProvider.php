<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MoodleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       $this->app->singleton(ClientAdapterInterface::class, function () {
            $connection = new Connection(config('moodle.connection.url'), config('moodle.connection.token'));
            return new RestClient($connection);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
