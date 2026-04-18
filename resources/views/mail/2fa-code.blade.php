<!DOCTYPE html>
<html>
<head>
    <title>2FA Login Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #B71C1C; text-align: center;">Bhopal Info Admin</h2>
        <p>Hello,</p>
        <p>You are receiving this email because a login attempt for your account requires Two-Factor Authentication.</p>
        <div style="background: #f4f4f4; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;">
            {{ $code }}
        </div>
        <p>This code will expire in 10 minutes.</p>
        <p>If you did not attempt to log in, please ignore this email or contact the system administrator.</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777; text-align: center;">
            &copy; {{ date('Y') }} Bhopal Info. All rights reserved.
        </p>
    </div>
</body>
</html>