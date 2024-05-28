<?php

// app/Http/Controllers/PhoneNumberController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;
use Exception;
use Twilio\Rest\Client;

class PhoneNumberController extends Controller
{
    public function showPhoneNumberForm()
    {
        return view('auth.phone_number');
    }

    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->twilio = new Client($sid, $token);
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

    public function storePhoneNumber(Request $request)
    {
        $request->validate(['phone_number' => 'required|unique:users,phone_number']);

        $verification_code = rand(100000, 999999);
        auth()->user()->update([
            'phone_number' => $request->phone_number,
            'two_factor_token' => $verification_code
        ]);

        // $twilio = new TwilioService();
        $this->sendSms($request->phone_number, "Your verification code is: $verification_code");

        return redirect()->route('phone.number.form')->with('phone_number_entered', true)->with('message', 'Verification code sent to your phone number.');
    }

    public function verifyPhoneNumber(Request $request)
    {
        $request->validate(['two_factor_token' => 'required']);

        if ($request->input('two_factor_token') == auth()->user()->two_factor_token) {
            auth()->user()->update(['two_factor_token' => null]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['two_factor_token' => 'The provided code is incorrect.']);
    }
}

// app/Http/Controllers/TwoFactorController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show2faForm()
    {
        return view('auth.2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate(['two_factor_token' => 'required']);

        if ($request->input('two_factor_token') == auth()->user()->two_factor_token) {
            auth()->user()->update(['two_factor_token' => null]);
            return redirect()->route('home');
        }

        return back()->withErrors(['two_factor_token' => 'The provided code is incorrect.']);
    }
}


