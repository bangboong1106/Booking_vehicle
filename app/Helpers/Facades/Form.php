<?php

namespace App\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Form
 * @package App\Helpers\Facades
 */
class Form extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'myForm';
    }
}
