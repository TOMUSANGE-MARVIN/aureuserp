<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('superadmin')->check()) {
            return redirect('/superadmin');
        }
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('superadmin')->user();

            if (! $user->is_superadmin) {
                Auth::guard('superadmin')->logout();
                return back()->withErrors(['email' => 'Access denied. This console is for platform administrators only.'])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect('/superadmin');
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/superadmin/login');
    }
}
