<?php

namespace App\Exceptions;

use Throwable;

class RedirectException extends \Exception
{
    protected $_backUrl = '';

    public function __construct($backUrl, $message = "", $code = 403, Throwable $previous = null)
    {
        $this->setBackUrl($backUrl);
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
