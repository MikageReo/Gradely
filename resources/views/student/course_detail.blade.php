<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->course_code }} - {{ $course->course_name }} - GRADELY</title>
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
        .sidebar {
            width: 250px;
            background: var(--color-primary);
            color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0,0,0,0.1);
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
            background: rgba(255,255,255,0.1);
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
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--color-primary);
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .course-header {
            background: var(--white);
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }
        .course-code {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .course-title {
            font-size: 28px;
            font-weight: 600;
            color: #222;
            margin-bottom: 12px;
        }
        .course-info {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
        }
        .section {
            background: var(--white);
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #222;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        .assignments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .assignment-card {
            background: var(--white);
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .assignment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--color-primary);
            transition: width 0.3s ease;
        }
        .assignment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-color: var(--color-primary);
        }
        .assignment-card:hover::before {
            width: 6px;
        }
        .assignment-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .assignment-title {
            font-weight: 600;
            color: #222;
            font-size: 16px;
            line-height: 1.4;
            margin: 0;
            flex: 1;
        }
        .assignment-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }
        .assignment-title a:hover {
            color: var(--color-primary);
        }
        .assignment-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 12px;
        }
        .badge-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .badge-warning {
            background: #fff3e0;
            color: #e65100;
        }
        .badge-info {
            background: #e3f2fd;
            color: #1565c0;
        }
        .badge-danger {
            background: #ffebee;
            color: #c62828;
        }
        .badge-secondary {
            background: #f5f5f5;
            color: #757575;
        }
        .assignment-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }
        .meta-item-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        .meta-item-value {
            color: #222;
            font-weight: 500;
        }
        .assignment-score {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            text-align: center;
        }
        .score-label {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .score-value {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 8px;
        }
        .score-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--color-primary);
        }
        .score-max {
            font-size: 18px;
            color: var(--muted);
        }
        .score-grade {
            font-size: 28px;
            font-weight: 700;
            color: var(--color-primary);
            margin-left: 8px;
        }
        .assignment-action {
            width: 100%;
            padding: 12px;
            background: var(--color-primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .assignment-action:hover {
            background: #1565C0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }
        .assignment-action:active {
            transform: translateY(0);
        }
        .no-score {
            text-align: center;
            padding: 16px;
            color: var(--muted);
            font-size: 14px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
            .main-content {
                padding: 20px;
            }
            .assignments-grid {
                grid-template-columns: 1fr;
            }
            .assignment-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .assignment-status-badge {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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
            <!-- Dashboard Link -->
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

            <a href="{{ route('profile.view') }}" class="{{ request()->routeIs('profile.view') ? 'active' : '' }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <main class="main-content">
            <a href="{{ route('student.dashboard') }}" class="back-link">
                ← Back to Dashboard
            </a>

            <div class="course-header">
                <div class="course-code">{{ $course->course_code }}</div>
                <h1 class="course-title">{{ $course->course_name }}</h1>
                <div class="course-info">
                    <div class="info-item">
                        <span>👨‍🏫</span>
                        <span>Lecturer(s):
                            @foreach($course->courseLecturers as $cl)
                                {{ $cl->lecturer->name }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                    <div class="info-item">
                        <span>📝</span>
                        <span>{{ $assignments->count() }} Assignments</span>
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            @php
                $perf = $performance ?? [];
                $totalAssignments = $perf['total_assignments'] ?? 0;
                $submittedCount = $perf['submitted_count'] ?? 0;
                $gradedCount = $perf['graded_count'] ?? 0;
                $averageScore = $perf['average_score'] ?? null;
                $averageGrade = $perf['average_grade'] ?? null;
                $completionPercentage = $perf['completion_percentage'] ?? 0;
                $perfLevel = $perf['performance_level'] ?? [];
                $hasGrades = $perf['has_grades'] ?? false;
            @endphp
            <div class="section" style="border-left: 4px solid {{ $perfLevel['color'] ?? '#1976D2' }};">
                <h2 class="section-title">📊 Course Performance</h2>

                <!-- Overall Performance Score Card -->
                @if($hasGrades && $averageScore !== null)
                    <div style="background: {{ $perfLevel['bg_color'] ?? '#F5F5F5' }}; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                            <div>
                                <div style="font-size: 13px; color: {{ $perfLevel['text_color'] ?? '#757575' }}; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Average Performance</div>
                                <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 8px;">
                                    <span style="font-size: 48px; font-weight: 700; color: {{ $perfLevel['color'] ?? '#1976D2' }};">{{ $averageScore }}</span>
                                    <span style="font-size: 20px; color: var(--muted);">/ 100</span>
                                    @if($averageGrade)
                                        <span style="font-size: 36px; font-weight: 700; color: {{ $perfLevel['color'] ?? '#1976D2' }}; margin-left: 8px;">({{ $averageGrade }})</span>
                                    @endif
                                </div>
                                <div style="font-size: 16px; font-weight: 600; color: {{ $perfLevel['text_color'] ?? '#757575' }};">{{ $perfLevel['level'] ?? 'No grades yet' }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 13px; color: {{ $perfLevel['text_color'] ?? '#757575' }}; margin-bottom: 4px;">Assignments Graded</div>
                                <div style="font-size: 32px; font-weight: 700; color: {{ $perfLevel['color'] ?? '#1976D2' }};">{{ $gradedCount }}</div>
                                <div style="font-size: 14px; color: var(--muted);">of {{ $totalAssignments }} total</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="background: #F5F5F5; border-radius: 12px; padding: 24px; margin-bottom: 24px; text-align: center;">
                        <div style="font-size: 16px; color: var(--muted); margin-bottom: 8px;">No grades available yet</div>
                        <div style="font-size: 14px; color: var(--muted);">Complete assignments and wait for grading to see your performance metrics</div>
                    </div>
                @endif

                <!-- Completion Progress Bar -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 600; color: #222;">Completion Progress</span>
                        <span style="font-size: 14px; font-weight: 600; color: var(--color-primary);">{{ $completionPercentage }}%</span>
                    </div>
                    <div style="width: 100%; height: 12px; background: #E0E0E0; border-radius: 6px; overflow: hidden; margin-bottom: 8px;">
                        <div style="width: {{ $completionPercentage }}%; height: 100%; background: linear-gradient(90deg, var(--color-primary) 0%, #42A5F5 100%); border-radius: 6px; transition: width 0.3s ease;"></div>
                    </div>
                    <div style="font-size: 13px; color: var(--muted);">
                        <strong>{{ $submittedCount }}</strong> of <strong>{{ $totalAssignments }}</strong> assignments submitted
                        @if($gradedCount > 0)
                            • <strong>{{ $gradedCount }}</strong> graded
                        @endif
                    </div>
                </div>

                <!-- Performance Level Indicator -->
                @if($hasGrades && $averageScore !== null)
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #222;">Performance Level</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: #E0E0E0; border-radius: 5px; overflow: hidden; position: relative; margin-bottom: 8px;">
                            <!-- Grade ranges background -->
                            <div style="position: absolute; width: 100%; height: 100%; display: flex;">
                                <div style="flex: 1; background: #F44336;"></div>
                                <div style="flex: 1; background: #FF9800;"></div>
                                <div style="flex: 1; background: #FFC107;"></div>
                                <div style="flex: 1; background: #2196F3;"></div>
                                <div style="flex: 1; background: #4CAF50;"></div>
                            </div>
                            <!-- Current performance indicator -->
                            @php
                                $scorePercent = min(100, max(0, $averageScore));
                                $indicatorPosition = ($scorePercent / 100) * 100;
                            @endphp
                            <div style="position: absolute; left: {{ $indicatorPosition }}%; top: -3px; width: 16px; height: 16px; background: {{ $perfLevel['color'] ?? '#1976D2' }}; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.3); transform: translateX(-50%); border: 3px solid white;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 12px; color: var(--muted);">F (0-49)</span>
                            </div>
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 12px; color: var(--muted);">D (50-59)</span>
                            </div>
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 12px; color: var(--muted);">C (60-69)</span>
                            </div>
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 12px; color: var(--muted);">B (70-79)</span>
                            </div>
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 12px; color: var(--muted);">A (80-100)</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="section">
                <h2 class="section-title">📝 Assignments</h2>
                @if($assignments->count() > 0)
                    <div class="assignments-grid">
                        @foreach($assignments as $assignment)
                            <div class="assignment-card">
                                <div class="assignment-card-header">
                                    <h3 class="assignment-title">
                                        <a href="{{ route('assignment.submission', $assignment->id) }}">
                                            {{ $assignment->title }}
                                        </a>
                                    </h3>
                                    @if($assignment->has_submission)
                                        @if($assignment->submission_status === 'marked')
                                            <span class="assignment-status-badge badge-success">
                                                ✓ Graded
                                            </span>
                                        @else
                                            <span class="assignment-status-badge badge-info">
                                                📤 Submitted
                                            </span>
                                        @endif
                                    @else
                                        @if($assignment->due_date && $assignment->due_date->isPast())
                                            <span class="assignment-status-badge badge-danger">
                                                ⚠️ Overdue
                                            </span>
                                        @else
                                            <span class="assignment-status-badge badge-warning">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <div class="assignment-meta">
                                    <div class="meta-item">
                                        <span class="meta-item-icon">📅</span>
                                        <span>
                                            <strong>Due:</strong>
                                            <span class="meta-item-value">
                                                {{ $assignment->due_date ? $assignment->due_date->format('M d, Y g:ia') : 'No due date' }}
                                            </span>
                                        </span>
                                    </div>
                                    @if($assignment->due_date)
                                        <div class="meta-item">
                                            <span class="meta-item-icon">
                                                @if($assignment->due_date->isPast())
                                                    ⏰
                                                @else
                                                    ⏱️
                                                @endif
                                            </span>
                                            <span>
                                                @if($assignment->due_date->isPast())
                                                    <span style="color: #c62828;">Overdue by {{ $assignment->due_date->diffForHumans() }}</span>
                                                @else
                                                    <span style="color: #2e7d32;">Due in {{ $assignment->due_date->diffForHumans() }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                @if($assignment->score !== null)
                                    <div class="assignment-score">
                                        <div class="score-label">Your Score</div>
                                        <div class="score-value">
                                            <span class="score-number">{{ number_format($assignment->score, 1) }}</span>
                                            <span class="score-max">/ 100</span>
                                            @if($assignment->grade)
                                                <span class="score-grade">({{ $assignment->grade }})</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="no-score">
                                        @if($assignment->has_submission)
                                            <span>⏳ Awaiting grading</span>
                                        @else
                                            <span>No submission yet</span>
                                        @endif
                                    </div>
                                @endif

                                <a href="{{ route('assignment.submission', $assignment->id) }}" class="assignment-action">
                                    @if($assignment->has_submission)
                                        View Submission
                                    @else
                                        Submit Assignment
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div style="font-size: 48px; margin-bottom: 16px;">📝</div>
                        <p style="font-size: 16px; color: var(--muted);">No assignments available for this course yet.</p>
                    </div>
                @endif
            </div>
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

    // Keep dropdown open if current page is a course page
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('coursesDropdown');
        const toggle = dropdown ? dropdown.previousElementSibling : null;

        @if(request()->routeIs('student.course.show'))
            // Open dropdown and highlight active course when viewing a course
            if (dropdown && toggle) {
                dropdown.classList.add('active');
                toggle.classList.add('active');
            }
        @endif
    });
</script>
</body>
</html>

