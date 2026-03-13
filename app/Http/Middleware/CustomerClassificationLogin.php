<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CustomerClassificationLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && (int)auth()->user()->is_classification_customer !== 1) {
            Session::flush();
            Auth::guard('web')->logout();
            return redirect()->route('register_online.index');
        }
        return $next($request);
    }
}
