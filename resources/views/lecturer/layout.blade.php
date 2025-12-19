<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Lecturer - GRADELY')</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font);
            background: var(--bg);
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--color-secondary);
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
            letter-spacing: 0.08em;
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
        .sidebar-lecturer-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }
        .sidebar-lecturer-email {
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
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.15);
            transform: translateX(2px);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 24px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
            transform: translateX(2px);
        }
        /* Dropdown */
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
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                padding: 20px;
            }
        }
    </style>
    @stack('styles')
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
                <div class="sidebar-lecturer-name">
                    {{ Auth::user()->name }}
                </div>
                <div class="sidebar-lecturer-email">
                    {{ Auth::user()->email }}
                </div>
            </div>
            
            <div class="sidebar-nav-label">Navigation</div>
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
            
            <a href="{{ route('profile.view') }}" class="{{ request()->routeIs('profile.view') ? 'active' : '' }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="success-alert" id="successAlert" style="position: fixed; top: 0; left: 0; right: 0; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; text-align: center; z-index: 1000; animation: slideDown 0.3s ease-out;">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

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

        // Keep dropdown open if current page is a course page, closed on dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('coursesDropdown');
            const toggle = dropdown ? dropdown.previousElementSibling : null;
            
            @if(request()->routeIs('lecturer.course.show'))
                // Open dropdown and highlight active course when viewing a course
                if (dropdown && toggle) {
                    dropdown.classList.add('active');
                    toggle.classList.add('active');
                }
            @elseif(request()->routeIs('lecturer.dashboard'))
                // Close dropdown when on dashboard
                if (dropdown && toggle) {
                    dropdown.classList.remove('active');
                    toggle.classList.remove('active');
                }
            @elseif(request()->routeIs('lecturer.courses'))
                // Open dropdown when on courses list page
                if (dropdown && toggle) {
                    dropdown.classList.add('active');
                    toggle.classList.add('active');
                }
            @endif
        });

        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 5000);
        }
    </script>
    @stack('scripts')
</body>
</html>

