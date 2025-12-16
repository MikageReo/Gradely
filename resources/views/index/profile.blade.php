<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - GRADELY</title>
    <style>
        :root {
            --color-primary: {{ Auth::user()->role === 'admin' ? '#C62828' : (Auth::user()->role === 'lecturer' ? '#00897B' : '#1976D2') }};
            --color-secondary: #00897B;
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
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 32px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 12px;
            font-weight: 600;
        }
        .sidebar-profile {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            margin-bottom: 28px;
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
        .sidebar-user-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }
        .sidebar-user-email {
            font-size: 12px;
            opacity: 0.9;
            word-break: break-all;
        }
        .sidebar-nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.7;
            margin: 8px 0 8px;
        }
        .sidebar a {
            display: block;
            color: var(--white);
            text-decoration: none;
            padding: 12px 14px;
            margin: 0 0 12px 0;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 14px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(2px);
        }
        .sidebar .logout {
            background: {{ Auth::user()->role === 'admin' ? '#8B0000' : 'rgba(255,0,0,0.3)' }};
            margin-top: 24px;
        }
        .sidebar .logout:hover {
            background: {{ Auth::user()->role === 'admin' ? '#A00000' : 'rgba(255,0,0,0.5)' }};
            transform: translateX(2px);
        }
        /* Dropdown for Lecturer */
        .dropdown {
            position: relative;
            margin: 0 0 12px 0;
        }
        .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
            padding: 12px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 14px;
        }
        .dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(2px);
        }
        .dropdown-toggle.active {
            background: rgba(255,255,255,0.15);
        }
        .dropdown-toggle::after {
            content: '▼';
            font-size: 10px;
            transition: transform 0.3s;
        }
        .dropdown-toggle.active::after {
            transform: rotate(180deg);
        }
        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            margin-left: 12px;
            margin-top: 8px;
        }
        .dropdown-menu.active {
            max-height: 500px;
        }
        .dropdown-menu a {
            padding: 10px 14px;
            font-size: 13px;
            border-left: 2px solid rgba(255,255,255,0.2);
            margin-left: 12px;
            margin-bottom: 6px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .dropdown-menu a:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: rgba(255,255,255,0.5);
            transform: translateX(2px);
        }
        .dropdown-menu a.active {
            background: rgba(255,255,255,0.15);
            border-left-color: var(--white);
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
            background: linear-gradient(135deg, var(--color-primary) 0%, {{ Auth::user()->role === 'admin' ? '#8B0000' : (Auth::user()->role === 'lecturer' ? '#00695C' : '#1565C0') }} 100%);
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
            background: {{ Auth::user()->role === 'admin' ? 'rgba(198, 40, 40, 0.08)' : (Auth::user()->role === 'lecturer' ? 'rgba(0, 137, 123, 0.08)' : 'rgba(25, 118, 210, 0.08)') }};
            color: {{ Auth::user()->role === 'admin' ? '#8B0000' : (Auth::user()->role === 'lecturer' ? '#00695C' : '#0d47a1') }};
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
            box-shadow: 0 0 0 1px {{ Auth::user()->role === 'admin' ? 'rgba(198, 40, 40, 0.25)' : (Auth::user()->role === 'lecturer' ? 'rgba(0, 137, 123, 0.25)' : 'rgba(25,118,210,0.25)') }};
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
            background: {{ Auth::user()->role === 'admin' ? '#8B0000' : (Auth::user()->role === 'lecturer' ? '#00695C' : '#1558a5') }};
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
                height: auto;
                position: relative;
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
        
        // Get lecturer courses for dropdown
        $lecturerCourses = collect();
        if ($role === 'lecturer') {
            $lecturerCourses = \App\Models\Courses::whereHas('courseLecturers', function($query) {
                $query->where('lecturer_id', Auth::id());
            })->with('courseLecturers', function($query) {
                $query->where('lecturer_id', Auth::id());
            })->get();
        }
    @endphp
    <div class="container">
        <h2>Edit Profile</h2>
        @if ($errors->any())
            <div class="alert alert-danger" id="flash-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success" id="flash-message">{{ session('success') }}</div>
        @endif
        <script>
            setTimeout(function() {
                var msg = document.getElementById('flash-message');
                if (msg) msg.style.display = 'none';
            }, 5000);
        </script>
        <style>
        .alert-success { background: #d4edda !important; color: #155724 !important; }
        .alert-danger { background: #f8d7da !important; color: #721c24 !important; }
        </style>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            <button type="submit">Update Profile</button>
        </form>
        <form method="GET" action="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'lecturer' ? route('lecturer.dashboard') : route('student.dashboard')) }}">
            <button type="submit" style="margin-top:10px;background:#aaa;color:#fff;">Back</button>
        </form>
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            
            <!-- Profile Card -->
            <div class="sidebar-profile">
                <div class="sidebar-avatar">
                    <span>👤</span>
                </div>
                <div class="sidebar-user-name">
                    {{ Auth::user()->name }}
                </div>
                <div class="sidebar-user-email">
                    {{ Auth::user()->email }}
                </div>
            </div>
            
            @if($role === 'lecturer')
                <!-- Dashboard Link -->
                <a href="{{ route('lecturer.dashboard') }}" class="{{ request()->routeIs('lecturer.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                
                <!-- My Courses Dropdown -->
                <div class="dropdown">
                    <div class="dropdown-toggle {{ request()->routeIs('lecturer.courses') || request()->routeIs('lecturer.course.show') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        📚 My Courses
                    </div>
                    <div class="dropdown-menu" id="coursesDropdown">
                        @php
                            // Get unique courses assigned to this lecturer
                            $lecturerCourses = \App\Models\Courses::whereHas('courseLecturers', function($query) {
                                $query->where('lecturer_id', Auth::id());
                            })->distinct()->get();
                        @endphp
                        @if($lecturerCourses->count() > 0)
                            @foreach($lecturerCourses as $course)
                                <a href="{{ route('lecturer.course.show', $course->id) }}" 
                                   class="{{ request()->routeIs('lecturer.course.show') && request()->route('courseId') == $course->id ? 'active' : '' }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('lecturer.courses') }}" style="opacity: 0.7;">
                                No courses assigned
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($role === 'student')
                <div class="sidebar-nav-label">Navigation</div>
                <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                
                <!-- My Courses Dropdown -->
                <div class="dropdown">
                    <div class="dropdown-toggle {{ request()->routeIs('student.course.show') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        📚 My Courses
                    </div>
                    <div class="dropdown-menu" id="coursesDropdown">
                        @php
                            // Get unique courses enrolled by this student
                            $studentCourses = \App\Models\Courses::whereHas('courseLecturers.students', function($query) {
                                $query->where('student_id', Auth::id());
                            })->distinct()->get();
                        @endphp
                        @if($studentCourses->count() > 0)
                            @foreach($studentCourses as $course)
                                <a href="{{ route('student.course.show', $course->id) }}" 
                                   class="{{ request()->routeIs('student.course.show') && request()->route('courseId') == $course->id ? 'active' : '' }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('student.dashboard') }}#courses" style="opacity: 0.7;">
                                No courses enrolled
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                <a href="{{ route('admin.new_student_registration') }}" class="{{ request()->routeIs('admin.new_student_registration') ? 'active' : '' }}">👤 Register Student</a>
                <a href="{{ route('admin.new_lecturer_registration') }}" class="{{ request()->routeIs('admin.new_lecturer_registration') ? 'active' : '' }}">👨‍🏫 Register Lecturer</a>
                <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">📚 Manage Courses</a>
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

    @if($role === 'lecturer')
    <script>
        function toggleDropdown(element) {
            const dropdown = element.nextElementSibling;
            const isActive = dropdown.classList.contains('active');
            
            // Close all dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('active');
            });
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.classList.remove('active');
            });
            
            // Toggle current dropdown
            if (!isActive) {
                dropdown.classList.add('active');
                element.classList.add('active');
            }
        }
    </script>
    @endif
</body>
</html>
