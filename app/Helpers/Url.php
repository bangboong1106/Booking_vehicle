<?php

namespace App\Helpers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Request;
use Route;


/**
 * Class Url
 * @package App\Helpers
 */
class Url
{
    protected static $_currentControllerName = null;
    /**
     * @var Url|null
     */
    protected static $_instance = null;
    /**
     * @var int
     */
    protected $_old = 0;
    /**
     *
     */
    const URl_KEY = 'url_key';
    /**
     *
     */
    const QUERY = '_o';
    /**
     *
     */
    const OLD_QUERY = '_o_';

    /**
     * @return Url|null
     */
    public static function getInstance()
    {
        if (!static::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return null
     */
    public static function getCurrentControllerName()
    {
        return self::$_currentControllerName;
    }

    /**
     * @param null $currentControllerName
     */
    public static function setCurrentControllerName($currentControllerName)
    {
        self::$_currentControllerName = $currentControllerName;
    }


    /**
     * @param Url|null $instance
     */
    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    /**
     * @param $default |null
     * @param $params |null
     * @return int
     */
    protected static function _genUrlKey($default = '', $params = array())
    {
        $url = static::_getFullUrl($default, $params);
        $urlKeys = session(self::URl_KEY, array());
        global $urlIdx;
        $urlIdx++;
        $time = time() . $urlIdx;
        krsort($urlKeys, SORT_STRING);
        if (!empty($urlKeys)) {
            $limit = getSystemConfig('back_url_limit', 99);
            $urlKeys = array_chunk($urlKeys, $limit - 1, true);
            $urlKeys = $urlKeys[0];
        }
        $urlKeys[$time] = $url;
        session(array(self::URl_KEY => $urlKeys));
        return $time;
    }

    protected static function _getFullUrl($default = '', $params = array())
    {
        if ($default) {
            $url = Route::has($default) ? route($default, $params) : $default;
            $url = parse_url($url);
            $r = isset($url['path']) ? $url['path'] : '';
            $r = isset($url['query']) && $r ? $r . '?' . $url['query'] : $r;
            $r = empty($url['fragment']) ? $r : $r . '#' . $url['fragment'];
            return $r;
        }
        try {
            $router = app()->make('router');
            $inputs = static::_buildParamString((array)Request::all());
            $uri = $router->getCurrentRoute()->uri;
            foreach ($router->getCurrentRoute()->parameters as $parameter => $value) {
                $uri = str_replace('{' . $parameter . '}', $value, $uri);
            }
            return $uri . $inputs;
        } catch (BindingResolutionException $e) {
            logError($e);
        }
        return '';
    }

    protected static function _buildParamString($params, $params1 = array())
    {
        $params = array_merge($params1, $params);
        $params = http_build_query($params);
        $params = $params ? '?' . $params : '';
        return $params;
    }

    /**
     * @param bool $full
     * @param string $defaultUrl
     * @return string
     */
    public static function getBackUrl($full = true, $defaultUrl = '')
    {
        $old = Request::get(self::QUERY, false);
        if (!$old) {
            return $defaultUrl ? $defaultUrl : url()->previous();
        }
        $urlKeys = session(self::URl_KEY, array());
        $url = isset($urlKeys[$old]) ? (strpos($urlKeys[$old], 'ajaxSearch') ? $defaultUrl : $urlKeys[$old]) : $defaultUrl;
        return $full ? url($url) : $url;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $default
     * @param array $paramsDefault
     * @return UrlGenerator|string
     */
    public static function backUrl($url, $params = array(), $default = '', $paramsDefault = [])
    {
        if (isset($params['backUrlKey'])) {
            $old = $params['backUrlKey'];
            unset($params['backUrlKey']);
        } else {
            if (isset($params['shown']) == true) {
                unset($params['backUrlKey']);
                return route($url, $params);
            } else {
                $old = self::_genUrlKey($default, $paramsDefault);
            }
        }

        $params = array_merge((array)$params, array(self::QUERY => $old));
        if (strpos($url, '/') !== false) {
            return url($url, $params);
        }
        return route($url, $params);
    }

    protected static function _getOldKey()
    {
        return static::getCurrentControllerName() . self::OLD_QUERY;
    }

    /**
     * @return mixed
     */
    public static function getOldUrl()
    {
        return session(static::_getOldKey(), '');
    }

    /**
     *
     */
    public static function collectOldUrl()
    {
        session([static::_getOldKey() => url()->previous()]);
    }

    /**
     * @return string
     */
    public static function keepBackUrl()
    {
        return '<input type="hidden" name="' . self::QUERY . '" value="' . Request::get(self::QUERY, '') . '">';
    }

    /**
     * @return int
     */
    public static function getBackUrlKey()
    {
        return self::_genUrlKey('', []);
    }

    public static function generateBackUrlKey($url = '', $params = [])
    {
        return self::_genUrlKey($url, $params);
    }
}