<?php

namespace Middleware;

use Src\Request;
use Src\Auth\Auth;  // Добавьте эту строку

class AdminOrSysadminMiddleware
{
    public function handle(Request $request, $param = null)
    {
        if (!Auth::check()) {
            app()->route->redirect('/login');
        }

        if (!in_array(Auth::user()->role_id, [1, 2])) {
            throw new \Exception('Доступ запрещен', 403);
        }
    }
}