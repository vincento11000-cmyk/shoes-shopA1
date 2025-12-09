<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\OtpTrait; // Add this
use App\Services\RecaptchaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use OtpTrait; // Add this
    
    protected $recaptchaService;

    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->recaptchaService = $recaptchaService;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate reCAPTCHA for registration
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Log::info('Registration attempt for: ' . $request->email);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info('User created with ID: ' . $user->id);

        // assign default role
        $user->assignRole('customer');

        // Send OTP for verification - FIXED: Use trait method
        Log::info('Sending OTP to new user: ' . $user->email);
        
        // FIX THIS LINE: Use $this->sendOtp() instead of $user->sendOtp()
        $otpSent = $this->sendOtp($user, 'verification'); // Changed here
        
        if (!$otpSent) {
            Log::error('Failed to send OTP during registration for user: ' . $user->email);
            // You might want to handle this error differently
        }

        // Login the user but they need to verify OTP
        Auth::login($user);

        Log::info('User logged in, redirecting to OTP verification');

        // Redirect to OTP verification page
        return redirect()->route('otp.verify.show');
    }
}