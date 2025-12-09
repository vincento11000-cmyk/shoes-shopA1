<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; border-radius: 5px; margin-top: 20px; }
        .otp-code { 
            font-size: 32px; 
            font-weight: bold; 
            color: #3b82f6; 
            text-align: center;
            letter-spacing: 10px;
            margin: 20px 0;
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #e5e7eb; 
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $userName }},</p>
            
            <p>Thank you for registering with {{ config('app.name') }}. Please use the following OTP code to verify your email address:</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <p>This OTP code will expire in {{ config('otp.expiry', 5) }} minutes.</p>
            
            <p>If you didn't request this registration, please ignore this email.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>