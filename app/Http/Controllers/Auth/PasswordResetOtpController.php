<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\OtpTrait;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    use OtpTrait;

    protected $recaptchaService;

    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->recaptchaService = $recaptchaService;
    }

    /**
     * Show forgot password form
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request - Send OTP
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate reCAPTCHA for password reset request
        if (!$this->recaptchaService->shouldSkip()) {
            $request->validate([
                'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                    if (!$this->recaptchaService->verify($value)) {
                        $fail('The reCAPTCHA verification failed. Please try again.');
                    }
                }],
            ]);
        }

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        Log::info('Password reset OTP requested', ['email' => $request->email]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Send OTP for password reset using the trait method
        $otpSent = $this->sendOtp($user, 'password_reset');

        if ($otpSent) {
            // Store email in session for verification step
            $request->session()->put('password_reset_email', $user->email);
            
            return redirect()->route('password.verify.otp')
                ->with('status', 'OTP sent to your email. Please check your inbox.');
        }

        return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
    }

    /**
     * Show OTP verification form for password reset
     */
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please request password reset first.']);
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        return view('auth.verify-password-otp', [
            'email' => $email,
            'hasOtp' => !empty($user->otp) && $user->otp_purpose === 'password_reset',
            'otpPurpose' => $user->otp_purpose ?? 'none',
        ]);
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $email = $request->session()->get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please request password reset again.']);
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        // Verify OTP using the trait method
        if ($this->verifyOtp($user, $request->otp, 'password_reset')) {
            // Generate a token for password reset
            $token = app('auth.password.broker')->createToken($user);
            
            // Store verification in session
            $request->session()->put('otp_verified', true);
            $request->session()->put('reset_token', $token);
            
            // Clear OTP from user using trait method
            $this->clearOtp($user);
            
            // Redirect to password reset form
            return redirect()->route('password.reset', ['token' => $token])
                ->with('status', 'OTP verified! You can now set your new password.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Resend OTP for password reset
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $otpSent = $this->sendOtp($user, 'password_reset');

        if ($otpSent) {
            return back()->with('status', 'OTP resent to your email.');
        }

        return back()->withErrors(['otp' => 'Failed to resend OTP. Please try again.']);
    }
}