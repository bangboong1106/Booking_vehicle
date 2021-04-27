<?php

namespace App\Exceptions;

use Throwable;

class PermissionException extends \Exception
{
    protected $_backUrl = '';

    public function __construct($message = "", $code = 403, Throwable $previous = null)
    {
        $message = $message ? $message : trans('auth.permission_denied');
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->_backUrl;
    }

    /**
     * @param string $backUrl
     */
    public function setBackUrl($backUrl)
    {
        $this->_backUrl = $backUrl;
    }


}
