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
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
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
        .assignments-table {
            width: 100%;
            border-collapse: collapse;
        }
        .assignments-table th,
        .assignments-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        .assignments-table th {
            font-weight: 600;
            color: var(--muted);
            font-size: 13px;
            text-transform: uppercase;
        }
        .assignments-table td {
            color: #222;
        }
        .assignment-title {
            font-weight: 500;
            color: var(--color-primary);
        }
        .assignment-title a {
            color: inherit;
            text-decoration: none;
        }
        .assignment-title a:hover {
            text-decoration: underline;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
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
            <div class="sidebar-nav-label">Navigation</div>
            <a href="{{ route('student.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('student.dashboard') }}#courses" class="active">📚 My Courses</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
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
                    <div class="info-item">
                        <span>👥</span>
                        <span>{{ $course->students_count ?? 0 }} Students</span>
                    </div>
                    <div class="info-item">
                        <span>🏛️</span>
                        <span>FACULTY OF COMPUTING</span>
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
                <h2 class="section-title">Assignments</h2>
                @if($assignments->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="assignments-table">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="assignment-title">
                                                <a href="{{ route('assignment.submission', $assignment->id) }}">
                                                    {{ $assignment->title }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y g:ia') : 'No due date' }}
                                        </td>
                                        <td>
                                            @if($assignment->has_submission)
                                                @if($assignment->submission_status === 'marked')
                                                    <span class="badge badge-success">Graded</span>
                                                @else
                                                    <span class="badge badge-info">Submitted</span>
                                                @endif
                                            @else
                                                @if($assignment->due_date && $assignment->due_date->isPast())
                                                    <span class="badge badge-danger">Overdue</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->score !== null)
                                                <strong>{{ $assignment->score }} / 100</strong>
                                                @if($assignment->grade)
                                                    <span style="font-weight: 600; color: var(--color-primary); margin-left: 8px;">({{ $assignment->grade }})</span>
                                                @endif
                                            @else
                                                <span style="color: var(--muted);">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('assignment.submission', $assignment->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>No assignments available for this course yet.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>

