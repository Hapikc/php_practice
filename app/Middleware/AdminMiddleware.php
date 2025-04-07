<?php

namespace Middleware;

use Src\Auth\Auth;
use Src\Request;

class AdminMiddleware
{
    public function handle(Request $request, $param = null)
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/hello');
        }
    }
}