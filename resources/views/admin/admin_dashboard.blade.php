<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Dashboard - GRADELY</title>
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
        .sidebar { width: 250px; background: var(--color-primary); color: var(--white); padding: 20px; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
        .sidebar h2 { font-size: 18px; margin-bottom: 32px; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 12px; letter-spacing: 0.08em; font-weight: 600; }
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
        .sidebar-admin-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }
        .sidebar-admin-email {
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
        .sidebar a { display: block; color: var(--white); text-decoration: none; padding: 12px 14px; margin: 0 0 12px 0; border-radius: 8px; transition: all 0.2s; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); transform: translateX(2px); }
        .sidebar .logout { background: #8B0000; margin-top: 24px; }
        .sidebar .logout:hover { background: #A00000; transform: translateX(2px); }
        .main-content { flex: 1; padding: 30px; }
        .header { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; color: #222; }
        .user-info { text-align: right; }
        .user-info p { color: var(--muted); font-size: 14px; }
        .user-name { font-weight: 600; color: #222; font-size: 16px; }
        .success-alert { position: fixed; top: 0; left: 0; right: 0; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; text-align: center; z-index: 1000; animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(0); opacity: 1; } to { transform: translateY(-100%); opacity: 0; } }
        .success-alert.hide { animation: slideUp 0.3s ease-out; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            background: linear-gradient(135deg, #C62828 0%, #8B0000 100%);
            padding: 20px 24px; 
            border-radius: 8px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.06); 
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--white);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(198, 40, 40, 0.3); }
        .stat-card-icon { font-size: 32px; flex-shrink: 0; }
        .stat-card-content { flex: 1; }
        .stat-card-value { font-size: 32px; font-weight: 700; color: var(--white); line-height: 1; margin-bottom: 4px; }
        .stat-card-label { font-size: 14px; color: rgba(255, 255, 255, 0.9); }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
        .card h3 { color: var(--color-primary); margin-bottom: 10px; }
        .card p { color: var(--muted); font-size: 14px; line-height: 1.6; }
        .recent-courses { background: var(--white); padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-top: 20px; }
        .recent-courses h3 { color: var(--color-primary); margin-bottom: 16px; font-size: 20px; }
        .course-item { padding: 12px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .course-item:last-child { border-bottom: none; }
        .course-info { flex: 1; }
        .course-code { font-weight: 600; color: #222; margin-bottom: 4px; }
        .course-name { font-size: 14px; color: var(--muted); }
        .course-stats { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .course-link { color: var(--color-primary); text-decoration: none; font-size: 14px; font-weight: 500; }
        .course-link:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; display: flex; justify-content: space-between; align-items: center; }
            .main-content { padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            
            <!-- Profile Card -->
            <div class="sidebar-profile">
                <div class="sidebar-avatar">
                    <span>👤</span>
                </div>
                <div class="sidebar-admin-name">
                    {{ Auth::user()->name }}
                </div>
                <div class="sidebar-admin-email">
                    {{ Auth::user()->email }}
                </div>
            </div>
            
            <div class="sidebar-nav-label">Navigation</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
            <a href="{{ route('admin.new_student_registration') }}">👤 Register Student</a>
            <a href="{{ route('admin.new_lecturer_registration') }}">👨‍🏫 Register Lecturer</a>
            <a href="{{ route('admin.courses.index') }}">📚 Manage Courses</a>
            <a href="{{ route('profile.view') }}" class="{{ request()->routeIs('profile.view') ? 'active' : '' }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="header">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">System overview and statistics</p>
                </div>
                <div class="user-info">
                    <p>Logged in as:</p>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-icon">👥</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value">{{ $totalStudents ?? 0 }}</div>
                        <div class="stat-card-label">Total Students</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">👨‍🏫</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value">{{ $totalLecturers ?? 0 }}</div>
                        <div class="stat-card-label">Total Lecturers</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">📚</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value">{{ $totalCourses ?? 0 }}</div>
                        <div class="stat-card-label">Total Courses</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">🎓</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value">{{ $totalEnrolledStudents ?? 0 }}</div>
                        <div class="stat-card-label">Enrolled Students</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="cards">
                <div class="card">
                    <h3>👥 Manage Users</h3>
                    <p>Add, edit, or remove students and lecturers from the system.</p>
                    <div style="margin-top: 12px;">
                        <a href="{{ route('admin.new_student_registration') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500; margin-right: 16px;">Register Student →</a>
                        <a href="{{ route('admin.new_lecturer_registration') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Register Lecturer →</a>
                    </div>
                </div>
                <div class="card">
                    <h3>📚 Manage Courses</h3>
                    <p>Create, update, or delete courses and assign lecturers to courses.</p>
                    <div style="margin-top: 12px;">
                        <a href="{{ route('admin.courses.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">View All Courses →</a>
                    </div>
                </div>
            </div>

            <!-- Recent Courses -->
            @if(isset($recentCourses) && $recentCourses->count() > 0)
            <div class="recent-courses">
                <h3>📚 Recent Courses</h3>
                @foreach($recentCourses as $course)
                    <div class="course-item">
                        <div class="course-info">
                            <div class="course-code">{{ $course->course_code }}</div>
                            <div class="course-name">{{ $course->course_name }}</div>
                            <div class="course-stats">
                                {{ $course->course_lecturers_count ?? 0 }} Lecturer(s) • {{ $course->total_students ?? 0 }} Student(s)
                            </div>
                        </div>
                        <a href="{{ route('admin.courses.show', $course->id) }}" class="course-link">View →</a>
                    </div>
                @endforeach
                @if($recentCourses->count() >= 5)
                    <div style="margin-top: 16px; text-align: center;">
                        <a href="{{ route('admin.courses.index') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">View All Courses →</a>
                    </div>
                @endif
            </div>
            @endif
        </main>
    </div>

    <script>
        // Auto-hide success alert after 5 seconds
        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.add('hide');
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
