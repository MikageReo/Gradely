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
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
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
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.15);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        /* Dropdown */
        .dropdown {
            position: relative;
        }
        .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
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
            margin-left: 10px;
            margin-top: 5px;
        }
        .dropdown-menu.active {
            max-height: 500px;
        }
        .dropdown-menu a {
            padding: 8px 12px;
            font-size: 14px;
            border-left: 2px solid rgba(255,255,255,0.2);
            margin-left: 10px;
        }
        .dropdown-menu a:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: rgba(255,255,255,0.5);
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
            <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 16px; margin-bottom: 20px; text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 8px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    👤
                </div>
                <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">{{ Auth::user()->name }}</div>
                <div style="font-size: 12px; opacity: 0.8;">{{ Auth::user()->email }}</div>
            </div>
            
            <!-- Dashboard Link -->
            <a href="{{ route('lecturer.dashboard') }}" class="{{ request()->routeIs('lecturer.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
            
            <!-- My Courses Dropdown -->
            <div class="dropdown">
                <div class="dropdown-toggle {{ request()->routeIs('lecturer.courses') || request()->routeIs('lecturer.course.show') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    📚 My Courses
                </div>
                <div class="dropdown-menu" id="coursesDropdown">
                    @php
                        $lecturerCourses = \App\Models\Courses::whereHas('courseLecturers', function($query) {
                            $query->where('lecturer_id', Auth::id());
                        })->with('courseLecturers', function($query) {
                            $query->where('lecturer_id', Auth::id());
                        })->get();
                    @endphp
                    @if($lecturerCourses->count() > 0)
                        @foreach($lecturerCourses as $course)
                            @foreach($course->courseLecturers as $cl)
                                <a href="{{ route('lecturer.course.show', $course->id) }}" 
                                   class="{{ request()->routeIs('lecturer.course.show') && request()->route('courseId') == $course->id ? 'active' : '' }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                    @if($cl->section)
                                        <small style="opacity: 0.8;">({{ $cl->section }})</small>
                                    @endif
                                </a>
                            @endforeach
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

        // Auto-hide success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 4000);
        }
    </script>
    @stack('scripts')
</body>
</html>

