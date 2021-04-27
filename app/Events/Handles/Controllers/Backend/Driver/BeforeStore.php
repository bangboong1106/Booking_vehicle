<?php

namespace App\Events\Handles\Controllers\Backend\Driver;

/**
 * Class AfterRender
 * @package App\Handles\Controllers\Home
 */
class BeforeStore
{

    public function handle($data)
    {
        $entity = $data;
        return $data;
    }
}