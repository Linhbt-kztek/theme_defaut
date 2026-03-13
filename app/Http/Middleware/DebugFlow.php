<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugFlow
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        \Log::info('MIDDLEWARE START', ['url' => $request->path()]);
        $response = $next($request);
        \Log::info('MIDDLEWARE END');
        return $response;
    }

}
