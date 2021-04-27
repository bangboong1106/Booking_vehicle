<?php

namespace App\Events\Handles\Controllers\Backend;

/**
 * Class AfterStoreOrder
 * @package App\Handles\Controllers\Home
 */
class BeforeDestroyCustomer
{

    public function handle($data)
    {
        app('App\Http\Controllers\Backend\CustomerController')->deleteRelation($data);
    }
}