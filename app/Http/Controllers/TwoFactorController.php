<?php

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
            // Clear the user's two_factor_token
            auth()->user()->update(['two_factor_token' => null]);
            // Set the session variable to indicate 2FA verification is successful
            session(['2fa_verified' => true]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['two_factor_token' => 'The provided code is incorrect.']);
    }
}



