<?php

namespace Middleware;

use Hapick\Middleware\AuthMiddleware as BaseAuthMiddleware;
use Src\Auth\Auth;

class AuthMiddleware extends BaseAuthMiddleware
{
    protected function authCheck(): bool
    {
        return Auth::check();
    }

    protected function redirect(string $url)
    {
        app()->route->redirect($url);
    }
}