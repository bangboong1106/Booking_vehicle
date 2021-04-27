<?php

namespace App\Events\Handles\Models\Base;
/**
 * Class BaseAfterSave
 * @package App\Handles\Controllers\Base
 */
class BaseAfterSave
{
    public function handle($data)
    {
        return $data;
    }
}