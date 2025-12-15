<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            font-family: var(--font);
            background: var(--bg);
            margin: 0;
        }
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .container {
            width: 100%;
            max-width: 780px;
            background: var(--white);
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.2fr);
            gap: 24px;
        }
        .profile-summary {
            padding-right: 8px;
            border-right: 1px solid #e5e7eb;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 38px;
            margin-bottom: 12px;
        }
        .profile-name {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .profile-role {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            margin-bottom: 10px;
        }
        .profile-email {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 16px;
            word-break: break-all;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(25, 118, 210, 0.08);
            color: #0d47a1;
            font-size: 12px;
            font-weight: 600;
        }
        h2 {
            margin-bottom: 12px;
            color: #111827;
            font-size: 18px;
        }
        .subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 13px;
            color: #374151;
        }
        input {
            width: 100%;
            padding: 10px 11px;
            margin-bottom: 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 1px rgba(25,118,210,0.25);
        }
        button {
            width: 100%;
            padding: 11px 14px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover {
            background: #1558a5;
        }
        .secondary-btn {
            margin-top: 8px;
            background: #6b7280 !important;
        }
        .alert {
            background: #fef2f2;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 13px;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        @media (max-width: 768px) {
            .container {
                grid-template-columns: minmax(0, 1fr);
                padding: 22px 18px;
            }
            .profile-summary {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 16px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <div class="profile-summary">
                <div class="profile-avatar">
                    <span>👤</span>
                </div>
                <div class="profile-name">{{ Auth::user()->name }}</div>
                <div class="profile-role">
                    {{ strtoupper(Auth::user()->role ?? 'User') }}
                </div>
                <div class="profile-email">{{ Auth::user()->email }}</div>
                <div style="margin-top: 8px;">
                    <span class="badge-pill">
                        ✨ Keep your details up to date
                    </span>
                </div>
            </div>
            <div>
                <h2>Account Settings</h2>
                <p class="subtitle">Update your personal information and change your password.</p>
                @if ($errors->any())
                    <div class="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password" placeholder="Enter a new password">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password">
                    <button type="submit">Save Changes</button>
                </form>
                <form method="GET" action="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'lecturer' ? route('lecturer.dashboard') : route('student.dashboard')) }}">
                    <button type="submit" class="secondary-btn">Back to Dashboard</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
