<?php

namespace App\Events\Listeners;

use App\Events\Handles\Controllers\Backend\AfterUpdateLocation;
use App\Events\Handles\Controllers\Backend\Driver\BeforeStore;

/**
 * Class Listeners
 * @package App\Events
 */
class ControllerListeners extends \App\Events\Listeners\Base\ControllerListeners
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->_listenControllerEvent('before_store.backend.driver', function ($arg) {
            return (new BeforeStore())->handle($arg);
        });
    }
}