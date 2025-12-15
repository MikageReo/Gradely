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
        .container {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--color-primary);
            color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
            letter-spacing: 0.08em;
        }
        .sidebar-profile {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            margin-bottom: 24px;
        }
        .sidebar-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        .sidebar-student-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }
        .sidebar-student-email {
            font-size: 12px;
            opacity: 0.9;
            word-break: break-all;
        }
        .sidebar-nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.7;
            margin: 6px 0 4px;
        }
        .sidebar a {
            display: block;
            color: var(--white);
            text-decoration: none;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.1);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        /* Main content */
        .main-content {
            flex: 1;
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-card {
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
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .main-content {
                padding: 20px;
                align-items: stretch;
            }
            .profile-card {
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
    @php
        $role = Auth::user()->role ?? 'student';
        $dashboardUrl = $role === 'admin'
            ? route('admin.dashboard')
            : ($role === 'lecturer' ? route('lecturer.dashboard') : route('student.dashboard'));
    @endphp
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <div class="sidebar-profile">
                <div class="sidebar-avatar">
                    <span>👤</span>
                </div>
                <div class="sidebar-student-name">
                    {{ Auth::user()->name }}
                </div>
                <div class="sidebar-student-email">
                    {{ Auth::user()->email }}
                </div>
            </div>
            <div class="sidebar-nav-label">Navigation</div>
            <a href="{{ $dashboardUrl }}">🏠 Dashboard</a>
            @if($role === 'student')
                <a href="{{ route('student.dashboard') }}#courses">📚 My Courses</a>
            @elseif($role === 'lecturer')
                <a href="{{ route('lecturer.courses') }}">📚 My Courses</a>
            @elseif($role === 'admin')
                <a href="{{ route('admin.courses.index') }}">📚 Courses</a>
            @endif
            <a href="{{ route('profile.view') }}" class="active">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="profile-card">
                <div class="profile-summary">
                    <div class="profile-avatar">
                        <span>👤</span>
                    </div>
                    <div class="profile-name">{{ Auth::user()->name }}</div>
                    <div class="profile-role">
                        {{ strtoupper($role ?? 'User') }}
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
                </div>
            </div>
        </main>
    </div>
</body>
</html>
