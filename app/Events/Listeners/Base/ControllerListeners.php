<?php

namespace App\Events\Listeners\Base;
use App\Events\Handles\Controllers\Base\BaseAfterRender;
use App\Events\Handles\Controllers\Base\BaseBeforeRender;


/**
 * Class Listeners
 * @package App\Events
 */
class ControllerListeners extends Listeners
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // all before_render
        $this->_listenControllerEvent('before_render.*', function ($arg) {
            return (new BaseBeforeRender())->handle($arg);
        });

        $this->_listenControllerEvent('after_render.*', function ($arg) {
            return (new BaseAfterRender())->handle($arg);
        });
    }
}