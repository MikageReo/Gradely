<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Admin - GRADELY')</title>
    <style>
        :root {
            --color-primary: #C62828;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font); background: var(--bg); }
        .container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--color-primary); color: var(--white); padding: 24px 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar h2 { font-size: 18px; margin-bottom: 32px; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 12px; font-weight: 600; }
        .sidebar a { display: block; color: var(--white); text-decoration: none; padding: 12px 14px; margin: 0 0 12px 0; border-radius: 8px; transition: all 0.2s; font-size: 14px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); transform: translateX(2px); }
        .sidebar a.active { background: rgba(255,255,255,0.2); font-weight: 600; }
        .sidebar .logout { background: #8B0000; margin-top: 24px; }
        .sidebar .logout:hover { background: #A00000; transform: translateX(2px); }
        .main-content { flex: 1; padding: 30px; margin-left: 250px; }
        .success-alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
        .error-alert { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; position: relative; height: auto; }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
            <a href="{{ route('admin.new_student_registration') }}" class="{{ request()->routeIs('admin.new_student_registration') ? 'active' : '' }}">👤 Register Student</a>
            <a href="{{ route('admin.new_lecturer_registration') }}" class="{{ request()->routeIs('admin.new_lecturer_registration') ? 'active' : '' }}">👨‍🏫 Register Lecturer</a>
            <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">📚 Manage Courses</a>
            <a href="{{ route('profile.view') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('partial_success'))
                <div class="success-alert">
                    {{ session('partial_success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-alert">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

