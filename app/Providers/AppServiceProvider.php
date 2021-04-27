<?php

namespace App\Providers;

use App\Model\Entities\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $registrar = new \App\Helpers\ResourceRegistrar($this->app['router']);

        $this->app->bind('Illuminate\Routing\ResourceRegistrar', function () use ($registrar) {
            return $registrar;
        });

        Order::observe(OrderObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        ////Facade to Object binding
        $this->app->bind('channellog', 'App\Helpers\ChannelWriter');
        $this->app->bind('mystorage', 'App\Helpers\MyStorage');
    }
}
