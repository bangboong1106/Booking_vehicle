<?php

namespace App\Events\Handles\Controllers\Frontend\Home;
use App\Events\Handles\Controllers\Base\BaseAfterRender;

/**
 * Class AfterRender
 * @package App\Handles\Controllers\Home
 */
class AfterRender extends BaseAfterRender
{

    public function handle($data)
    {
        return $data;
    }
}