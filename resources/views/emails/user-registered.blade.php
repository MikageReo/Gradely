<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Gradely</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #1976D2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #1976D2;
            margin: 20px 0;
        }
        .credentials-item {
            margin: 10px 0;
            padding: 8px 0;
        }
        .credentials-item strong {
            color: #1976D2;
            display: inline-block;
            min-width: 100px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .warning p {
            margin: 0;
            color: #856404;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Welcome to Gradely!</h1>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>

            <p>Your account has been successfully created. You can now log in to the system using the following credentials:</p>

            <div class="credentials">
                <div class="credentials-item">
                    <strong>Email:</strong> {{ $user->email }}
                </div>
                <div class="credentials-item">
                    <strong>Password:</strong> {{ $password }}
                </div>
                <div class="credentials-item">
                    <strong>Role:</strong> {{ ucfirst($user->role) }}
                </div>
            </div>

            <div class="warning">
                <p>⚠️ Please change your password after first login for security.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login Now</a>
            </div>

            <p>If you have any questions or need assistance, please contact the administration.</p>

            <p>Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>

