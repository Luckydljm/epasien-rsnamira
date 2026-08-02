<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Redirect ke dashboard jika sudah login
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('auth_user')) {
            return redirect()->route('portal');
        }
        return $next($request);
    }
}
