<?php

namespace App\Traits;

use App\Mail\OtpVerificationMail;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait OtpTrait
{
    /**
     * Generate OTP
     */
    protected function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

   /**
 * Send OTP via email - SIMPLIFIED VERSION
 */
protected function sendOtp($user, $purpose = 'verification'): bool
{
    try {
        $otp = $this->generateOtp();
        $expiryMinutes = 5;
        
        Log::info('=== SENDING OTP ===');
        Log::info("User: {$user->email}, Purpose: {$purpose}");
        Log::info("Generated OTP: {$otp}");
        
        // SIMPLE DIRECT DATABASE UPDATE
        $result = DB::table('users')
            ->where('id', $user->id)
            ->update([
                'otp' => $otp,
                'otp_purpose' => $purpose,
                'otp_expires_at' => now()->addMinutes($expiryMinutes),
                'updated_at' => now(),
            ]);
        
        Log::info("DB Update Result: {$result} rows affected");
        
        // Verify update
        $dbCheck = DB::table('users')
            ->where('id', $user->id)
            ->select('otp', 'otp_purpose', 'otp_expires_at')
            ->first();
        
        Log::info("DB Check - OTP: " . ($dbCheck->otp ?? 'NULL'));
        Log::info("DB Check - Purpose: " . ($dbCheck->otp_purpose ?? 'NULL'));
        Log::info("DB Check - Expires: " . ($dbCheck->otp_expires_at ?? 'NULL'));
        
        // IMPORTANT: Refresh the user model
        $user->refresh();
        Log::info("User model after refresh - OTP: {$user->otp}, Purpose: {$user->otp_purpose}");
        
        // Send email
        if ($purpose === 'password_reset') {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
        } else {
            Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));
        }
        
        Log::info('Email sent successfully');
        Log::info('=== OTP SEND COMPLETE ===');
        
        return true;

    } catch (\Exception $e) {
        Log::error('OTP SEND ERROR: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return false;
    }
}

    /**
     * Verify OTP - SIMPLIFIED VERSION
     */
    protected function verifyOtp($user, $otp, $purpose = 'verification'): bool
    {
        Log::info('=== VERIFYING OTP ===');
        Log::info("User: {$user->email}, Entered OTP: {$otp}");
        Log::info("Expected Purpose: {$purpose}");
        
        // Get fresh data from database
        $dbUser = DB::table('users')
            ->where('id', $user->id)
            ->select('otp', 'otp_purpose', 'otp_expires_at', 'email_verified_at')
            ->first();
        
        Log::info("DB OTP: " . ($dbUser->otp ?? 'NULL'));
        Log::info("DB Purpose: " . ($dbUser->otp_purpose ?? 'NULL'));
        Log::info("DB Expires: " . ($dbUser->otp_expires_at ?? 'NULL'));
        
        // Check if OTP exists
        if (!$dbUser->otp) {
            Log::warning('No OTP in database');
            return false;
        }
        
        // Check purpose
        if ($dbUser->otp_purpose !== $purpose) {
            Log::warning("Purpose mismatch. Expected: {$purpose}, Got: {$dbUser->otp_purpose}");
            return false;
        }
        
        // Check expiry
        if ($dbUser->otp_expires_at && now()->greaterThan($dbUser->otp_expires_at)) {
            Log::warning('OTP expired');
            return false;
        }
        
        // Check match
        if ($dbUser->otp === $otp) {
            Log::info('OTP MATCHES!');
            
            if ($purpose === 'verification') {
                // Update database
                $updateResult = DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'email_verified_at' => now(),
                        'otp' => null,
                        'otp_purpose' => null,
                        'otp_expires_at' => null,
                        'updated_at' => now(),
                    ]);
                
                Log::info("Verification update result: {$updateResult} rows affected");
                
                // Verify update
                $verifyCheck = DB::table('users')
                    ->where('id', $user->id)
                    ->select('email_verified_at')
                    ->first();
                
                Log::info("Email verified at: " . ($verifyCheck->email_verified_at ?? 'NULL'));
            }
            
            Log::info('=== VERIFICATION SUCCESS ===');
            return true;
        }
        
        Log::warning('OTP does not match');
        return false;
    }

    /**
     * Clear OTP
     */
    protected function clearOtp($user): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'otp' => null,
                'otp_purpose' => null,
                'otp_expires_at' => null,
            ]);
    }
}