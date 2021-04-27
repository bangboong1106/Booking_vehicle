<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Base\FrontendController;

class CustomerController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        if (!frontendGuard()->check()) {
            return redirect($this->_getBackUrl(request()));
        }
    }

    public function index()
    {
    }
}