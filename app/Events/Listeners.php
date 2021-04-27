<?php

namespace App\Events;

use App\Events\Listeners\ControllerListeners;
use App\Events\Listeners\ModelListeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class Listeners
 * @package App\Events
 */
class Listeners extends ServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        (new ControllerListeners())->boot();
        (new ModelListeners())->boot();
    }
}