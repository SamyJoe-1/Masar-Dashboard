<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Check if user already exists
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // Log in the existing user
                Auth::login($existingUser);
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => hash('sha256', $user->password ?? '-'),
                ]);
                Auth::login($user);
            }
            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
