<?php

namespace App\Events\Listeners\Base;
use App\Events\Handles\Models\Base\BaseAfterSave;
use App\Events\Handles\Models\Base\BaseBeforeSave;


/**
 * Class Listeners
 * @package App\Events
 */
class ModelListeners extends Listeners
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // all before_render
        $this->_listenModelEvent('before_save.*', function ($arg) {
            return (new BaseBeforeSave())->handle($arg);
        });

        $this->_listenModelEvent('after_save.*', function ($arg) {
            return (new BaseAfterSave())->handle($arg);
        });
    }
}