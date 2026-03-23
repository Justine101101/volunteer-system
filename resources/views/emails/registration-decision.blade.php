<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Update</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; padding: 24px; background-color: #f8fafc;">
<div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
    <h2 style="margin: 0 0 12px; font-size: 20px; color: #0f172a;">
        {{ $status === 'approved' ? 'Registration Approved' : 'Registration Declined' }}
    </h2>

    <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563;">
        Event: <strong>{{ $eventTitle }}</strong>
    </p>

    <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563;">
        {{ $status === 'approved' ? 'Your registration was approved.' : 'Your registration was declined.' }}
    </p>

    <p style="margin: 0; font-size: 13px; color: #6b7280;">
        You can log in to view details in your dashboard.
    </p>
</div>
</body>
</html>

