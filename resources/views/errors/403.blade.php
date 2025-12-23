<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Forbidden - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --error-color: #E53935;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font);
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 50%, #90CAF9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            padding: 48px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            font-size: 120px;
            margin-bottom: 24px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: var(--error-color);
            margin-bottom: 16px;
            line-height: 1;
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #222;
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .error-details {
            background: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 32px;
            text-align: left;
        }

        .error-details-title {
            font-weight: 600;
            color: #E65100;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .error-details-text {
            color: #222;
            font-size: 14px;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--color-primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #1565C0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
        }

        .btn-secondary:hover {
            background: #E3F2FD;
            transform: translateY(-2px);
        }

        .help-text {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #E0E0E0;
            font-size: 13px;
            color: var(--muted);
        }

        .help-text a {
            color: var(--color-primary);
            text-decoration: none;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 32px 24px;
            }

            .error-icon {
                font-size: 80px;
            }

            .error-code {
                font-size: 48px;
            }

            .error-title {
                font-size: 22px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🚫</div>
        <div class="error-code">403</div>
        <h1 class="error-title">Access Forbidden</h1>
        <p class="error-message">
            @if(isset($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
            @else
                You don't have permission to access this resource.
            @endif
        </p>

        @if(isset($exception) && $exception->getMessage())
            <div class="error-details">
                <div class="error-details-title">Why am I seeing this?</div>
                <div class="error-details-text">
                    {{ $exception->getMessage() }}
                </div>
            </div>
        @endif

        <div class="action-buttons">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                ← Go Back
            </a>
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        🏠 Go to Dashboard
                    </a>
                @elseif(Auth::user()->role === 'lecturer')
                    <a href="{{ route('lecturer.dashboard') }}" class="btn btn-primary">
                        🏠 Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                        🏠 Go to Dashboard
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    🔐 Login
                </a>
            @endauth
        </div>

        <div class="help-text">
            If you believe this is an error, please contact your administrator or 
            <a href="mailto:support@gradely.com">support@gradely.com</a>
        </div>
    </div>
</body>
</html>

