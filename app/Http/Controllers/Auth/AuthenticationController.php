<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class AuthenticationController extends Controller
{
    public function aio()
    {
        return view('auth.aio');
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }

    public function profile()
    {
        return view('dashboard.user.profile');
    }

}
