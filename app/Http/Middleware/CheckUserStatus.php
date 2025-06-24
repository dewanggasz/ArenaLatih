<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika ada pengguna yang login DAN perannya adalah 'pre-register'
        if (Auth::check() && Auth::user()->role === 'pre-register') {
            
            // Izinkan akses ke halaman tunggu itu sendiri atau ke proses logout
            // Ini untuk mencegah 'infinite redirect loop'
            if (!$request->routeIs('pending.approval') && !$request->routeIs('logout')) {
                // Jika mencoba mengakses halaman lain, lempar ke halaman tunggu
                return redirect()->route('pending.approval');
            }
        }
        
        // Jika bukan pengguna 'pre-register', izinkan lewat
        return $next($request);
    }
}