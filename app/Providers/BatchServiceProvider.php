<?php

namespace App\Providers;

use App\Services\Batch\Batch;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class BatchServiceProvider extends ServiceProvider
{
    /**
     * Register Batch instance to IOC.
     *
     * @updateedBy Ibrahim Sakr <ebrahimes@gmail.com>
     */
    public function register()
    {
        $this->app->bind('Batch', function ($app) {
            return new Batch($app->make(DatabaseManager::class));
        });
    }
}
