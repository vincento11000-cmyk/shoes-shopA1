<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;

    public function __construct($otp, $userName)
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Password Reset OTP - ' . config('app.name'))
                    ->view('emails.password-reset-otp')
                    ->with([
                        'otp' => $this->otp,
                        'userName' => $this->userName,
                    ]);
    }
}