<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    Public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login dan memiliki role admin
        if (Auth::check() && (Auth::user()->role !== 'admin')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
        
    }
}
