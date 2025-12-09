<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Check if OTP was verified via session
        if (!$request->session()->get('otp_verified')) {
            // If no OTP verification, use Laravel's default token-based reset
            return view('auth.reset-password', [
                'request' => $request,
            ]);
        }

        // OTP verified flow
        $email = $request->session()->get('password_reset_email');
        $token = $request->session()->get('reset_token') ?? $request->token;

        if (!$email || !$token) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if this is an OTP-verified reset
        $isOtpVerified = $request->session()->get('otp_verified');
        
        if ($isOtpVerified) {
            // OTP verified flow
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            // Reset password
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

            // Clear all session data
            $request->session()->flush();

            return redirect()->route('login')->with('status', 'Your password has been reset! You can now login with your new password.');
        } else {
            // Standard Laravel password reset flow
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            return $status == Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
        }
    }
}