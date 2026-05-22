<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('superadmin')->check()) {
            return redirect('/superadmin/login');
        }

        if (! Auth::guard('superadmin')->user()->is_superadmin) {
            Auth::guard('superadmin')->logout();
            return redirect('/superadmin/login')->withErrors(['email' => 'Access denied.']);
        }

        return $next($request);
    }
}
