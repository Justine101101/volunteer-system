<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Your Account</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; padding: 24px; background-color: #f8fafc;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
        <h2 style="margin: 0 0 12px; font-size: 20px; color: #0f172a;">Verify Your Account</h2>
        <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563;">
            Your verification code is:
        </p>
        <p style="margin: 0 0 16px; font-size: 28px; font-weight: 700; letter-spacing: 0.3em; color: #0f172a;">
            {{ $otp }}
        </p>
        <p style="margin: 0; font-size: 13px; color: #6b7280;">
            This code will expire in <strong>5 minutes</strong>. If you did not request this, you can safely ignore this email.
        </p>
    </div>
</body>
</html>

