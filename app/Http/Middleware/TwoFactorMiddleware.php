<?php

// app/Http/Middleware/TwoFactorMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            if (!$user->phone_number) {
                return redirect()->route('phone.number.form');
            }

            if (session('2fa_verified') !== true) {
                if ($user->two_factor_token === null) {
                    // Generate a new 2FA token
                    $verification_code = rand(100000, 999999);
                    $user->update(['two_factor_token' => $verification_code]);

                    // Send the token via SMS
                    app('App\Services\TwilioService')->sendSms($user->phone_number, "Your verification code is: $verification_code");
                }

                return redirect()->route('2fa.form');
            }
        }

        return $next($request);
    }
}




