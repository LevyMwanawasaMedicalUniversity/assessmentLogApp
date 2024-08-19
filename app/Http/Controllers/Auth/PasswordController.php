<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                Password::min(8) // Ensure a minimum length of 8 characters
                    ->letters() // Ensure at least one letter
                    ->mixedCase() // Ensure both uppercase and lowercase letters
                    ->numbers() // Ensure at least one number
                    ->symbols(), // Ensure at least one special character
                'confirmed'
            ],
        ]);

        $user = $request->user();
        $firstLogin = $user->password_changed_at;
        $user->password_changed_at = now();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if($firstLogin === null) {
            return redirect()->route('dashboard')->with('success', 'Password reset successfully. Please update your profile.');
        }else{
            return redirect()->back()->with('success', 'Password reset successfully.');
        }       
    }
}
