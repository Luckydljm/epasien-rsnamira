<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Cek apakah user sudah login via session custom
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('auth_user')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Rute portal & logout selalu bisa diakses tanpa session active_portal
        if ($request->routeIs('portal*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Jika belum memilih portal, paksa balik ke portal!
        if (! session()->has('active_portal')) {
            return redirect()->route('portal')
                ->with('error', 'Silakan pilih pelayanan Rawat Jalan atau Rawat Inap terlebih dahulu.');
        }

        return $next($request);
    }
}
