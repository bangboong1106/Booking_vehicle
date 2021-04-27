<?php

namespace App\Events\Handles\Controllers\Frontend\Home;
use App\Events\Handles\Controllers\Base\BaseBeforeRender;

/**
 * Class BeforeRender
 * @package App\Handles\Controllers\Home
 */
class BeforeRender extends BaseBeforeRender
{

    public function handle($data)
    {
        return $data;
    }
}