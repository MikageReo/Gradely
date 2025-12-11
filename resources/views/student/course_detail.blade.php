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
            <a href="{{ route('student.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('student.dashboard') }}#courses" class="active">📚 My Courses</a>
            <a href="#grades">📊 Grades</a>
            <a href="#assignments">✏️ Assignments</a>
            <a href="#progress">📈 Progress</a>
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

