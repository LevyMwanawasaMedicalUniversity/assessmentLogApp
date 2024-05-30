<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Exception;

class TwoFactorMiddleware
{
    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->twilio = new Client($sid, $token);
    }

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
                    $verification_code = encrypt($verification_code);
                    $user->update(['two_factor_token' => $verification_code]);

                    // Send the token via SMS
                    $this->sendSms($user->phone_number, "Your verification code is: $verification_code");
                }

                return redirect()->route('2fa.form');
            }
        }

        return $next($request);
    }

    public function sendSms($receiverNumber, $message)
    {
        $fromNumber = env('TWILIO_PHONE_NUMBER');

        try {
            $this->twilio->messages->create($receiverNumber, [
                'from' => $fromNumber,
                'body' => $message
            ]);

            return 'SMS Sent Successfully.';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}