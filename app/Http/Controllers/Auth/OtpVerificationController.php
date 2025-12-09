<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\OtpTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    use OtpTrait;

    /**
     * Show OTP verification form
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        Log::info("=== OTP Verification Page Accessed ===");
        Log::info("User: {$user->email}");

        // Get fresh data from database
        $dbUser = DB::table('users')
            ->where('id', $user->id)
            ->select('otp', 'otp_purpose', 'otp_expires_at', 'email_verified_at')
            ->first();

        Log::info("Database check:");
        Log::info("- OTP: " . ($dbUser->otp ?? 'NULL'));
        Log::info("- Purpose: " . ($dbUser->otp_purpose ?? 'NULL'));
        Log::info("- Expires: " . ($dbUser->otp_expires_at ?? 'NULL'));
        Log::info("- Is expired: " . ($dbUser->otp_expires_at && now()->greaterThan($dbUser->otp_expires_at) ? 'YES' : 'NO'));

        // Check if we need to send OTP
        $shouldSendOtp = false;
        
        // Condition 1: No OTP
        if (!$dbUser->otp) {
            Log::info("No OTP found - will send new one");
            $shouldSendOtp = true;
        }
        // Condition 2: Wrong purpose
        elseif ($dbUser->otp_purpose !== 'verification') {
            Log::info("OTP has wrong purpose ({$dbUser->otp_purpose}) - will send new one");
            $shouldSendOtp = true;
        }
        // Condition 3: OTP expired
        elseif ($dbUser->otp_expires_at && now()->greaterThan($dbUser->otp_expires_at)) {
            Log::info("OTP expired at {$dbUser->otp_expires_at} - will send new one");
            $shouldSendOtp = true;
        }
        // Condition 4: Email already verified
        elseif ($dbUser->email_verified_at) {
            Log::info("Email already verified - redirecting to home");
            return redirect('/');
        }

        if ($shouldSendOtp) {
            Log::info("Sending new OTP...");
            $this->sendOtp($user, 'verification');
        } else {
            Log::info("Valid OTP exists, not sending new one");
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
            'otpExists' => !empty($dbUser->otp),
            'otpExpired' => $dbUser->otp_expires_at && now()->greaterThan($dbUser->otp_expires_at)
        ]);
    }

    /**
     * Handle OTP verification
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        Log::info("=== OTP Verification Attempt ===");
        Log::info("User: {$user->email}");
        Log::info("Entered OTP: {$request->otp}");

        if ($this->verifyOtp($user, $request->otp, 'verification')) {
            Log::info("OTP verification SUCCESS for {$user->email}");
            return redirect('/')
                ->with('success', 'Email verified successfully!');
        }

        Log::warning("OTP verification FAILED for {$user->email}");
        return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        Log::info("=== Resending OTP ===");
        Log::info("User: {$user->email}");
        
        $this->sendOtp($user, 'verification');

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}