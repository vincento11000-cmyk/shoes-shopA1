<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #dc2626; color: white; padding: 20px; text-align: center;">
            <h1>Password Reset Request</h1>
        </div>
        
        <div style="background: #fef2f2; padding: 30px; border-radius: 5px; margin-top: 20px;">
            <p>Hello {{ $userName }},</p>
            
            <p>We received a request to reset your password for your {{ config('app.name') }} account.</p>
            
            <p>Please use the following OTP code to verify your identity:</p>
            
            <div style="font-size: 32px; font-weight: bold; color: #dc2626; text-align: center; letter-spacing: 10px; margin: 20px 0;">
                {{ $otp }}
            </div>
            
            <p><strong>Important:</strong> This OTP code will expire in 10 minutes.</p>
            
            <div style="background: #fff; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <p><strong>To reset your password:</strong></p>
                <ol style="margin-left: 20px;">
                    <li>Go to the OTP verification page</li>
                    <li>Enter the OTP code: <strong>{{ $otp }}</strong></li>
                    <li>Set your new password</li>
                </ol>
                <p style="text-align: center; margin-top: 15px;">
                    <a href="{{ url('/verify-password-otp') }}" 
                       style="background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        Click here to enter OTP
                    </a>
                </p>
            </div>
            
            <p>If you didn't request a password reset, please ignore this email.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>