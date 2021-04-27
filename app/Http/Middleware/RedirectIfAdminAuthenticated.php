<?php

namespace App\Http\Middleware;
/**
 * Class RedirectIfAdminAuthenticated
 * @package App\Http\Middleware
 */
class RedirectIfAdminAuthenticated extends Authenticated
{
    public function init()
    {
        $this->setGuard(backendGuard());
    }
}
