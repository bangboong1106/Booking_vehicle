<?php

namespace App\Http\Controllers\Base;

use App\Helpers\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Redirect;

/**
 * Class FrontendController
 * @package App\Http\Controllers\Base
 */
class FrontendController extends BaseController
{
    /**
     * @var string
     */
    protected $_area = 'frontend';

    public function index()
    {
        return $this->render();
    }

    public function getCurrentUser()
    {
        return frontendGuard()->user();
    }

    protected function _toLogin($request)
    {
        return redirect($this->_getBackUrl($request));
    }

    /**
     * @param $request
     * @return string
     */
    protected function _getBackUrl($request)
    {
        $params = ['return_url' => $request->fullUrl()];
        $url = route('frontend.login', $params);
        return $url;
    }
}
