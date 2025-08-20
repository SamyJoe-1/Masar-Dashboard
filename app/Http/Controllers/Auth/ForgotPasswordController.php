<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        session(['path' => 'reset']);
        return view('auth.aio');
    }

    // Override the method to add debugging
    public function sendResetLinkEmail(Request $request)
    {
        Log::info('Password reset attempt for email: ' . $request->email);

        $this->validateEmail($request);

        // Check if user exists
        $user = \App\Models\User::where('email', $request->email)->first();
        Log::info('User found: ' . ($user ? 'Yes' : 'No'));

        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        Log::info('Password reset broker response: ' . $response);

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
