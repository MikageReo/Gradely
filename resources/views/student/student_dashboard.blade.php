<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Student Dashboard - GRADELY</title>
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
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .header {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #222;
        }
        .user-info {
            text-align: right;
        }
        .user-info p {
            color: var(--muted);
            font-size: 14px;
        }
        .user-name {
            font-weight: 600;
            color: #222;
            font-size: 16px;
        }
        .success-alert {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 16px;
            text-align: center;
            z-index: 1000;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
        .success-alert.hide {
            animation: slideUp 0.3s ease-out;
        }
        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .card h3 {
            color: var(--color-primary);
            margin-bottom: 10px;
        }
        .card p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }
        .assignments-card {
            margin-top: 20px;
        }
        .assignments-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .assignments-meta h3 {
            margin: 0;
        }
        .assignments-meta .muted {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 4px;
        }
        .pill-count {
            background: rgba(25,118,210,0.12);
            color: #0d47a1;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13px;
        }
        .table-wrapper {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        .table th {
            color: #555;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            background: #f8fafc;
        }
        .assignment-title {
            font-weight: 600;
            color: #222;
        }
        .assignment-title a:hover {
            color: var(--color-primary);
            text-decoration: underline;
        }
        .assignment-course {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }
        .due-date {
            white-space: nowrap;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
        }
        .status-badge.info {
            background: rgba(25,118,210,0.12);
            color: #0d47a1;
        }
        .status-badge.danger {
            background: rgba(229,57,53,0.12);
            color: #c62828;
        }
        .empty-state {
            padding: 16px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #555;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .main-content {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
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
            <a href="#top">🏠 Dashboard</a>
            <a href="#courses" onclick="document.getElementById('courses').scrollIntoView({behavior: 'smooth'}); return false;">📚 My Courses</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="top">
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    {{ session('success') }}
                </div>
            @endif
            @php
                $pendingAssignments = $pendingAssignments ?? collect();
            @endphp

            <div class="header">
                <div>
                    <h1>Welcome to Your Dashboard</h1>
                </div>
                <div class="user-info">
                    <p>Logged in as:</p>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                </div>
            </div>

            <!-- Overall Performance Summary -->
            @php
                $allPerformance = $courses->map(fn($c) => $c->performance)->filter();
                $overallAvgScore = $allPerformance->where('has_grades', true)->pluck('average_score')->filter()->avg();
                $totalGraded = $allPerformance->sum('graded_count');
                $totalSubmitted = $allPerformance->sum('submitted_count');
                $totalAssignments = $allPerformance->sum('total_assignments');
                $overallCompletion = $totalAssignments > 0 ? round(($totalSubmitted / $totalAssignments) * 100) : 0;
                $overallGrade = null;
                if ($overallAvgScore !== null) {
                    if ($overallAvgScore >= 80) $overallGrade = 'A';
                    elseif ($overallAvgScore >= 70) $overallGrade = 'B';
                    elseif ($overallAvgScore >= 60) $overallGrade = 'C';
                    elseif ($overallAvgScore >= 50) $overallGrade = 'D';
                    else $overallGrade = 'F';
                }
            @endphp
            @if($courses->count() > 0 && $overallAvgScore !== null)
                <div class="card" id="progress" style="margin-bottom: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <h3 style="color: white; margin-bottom: 8px; font-size: 18px;">📈 Overall Performance</h3>
                            <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0;">Your academic performance across all courses</p>
                        </div>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <span style="font-size: 42px; font-weight: 700; color: white;">{{ round($overallAvgScore, 1) }}</span>
                            <span style="font-size: 20px; color: rgba(255,255,255,0.8);">/ 100</span>
                            @if($overallGrade)
                                <span style="font-size: 32px; font-weight: 700; color: white; margin-left: 8px;">({{ $overallGrade }})</span>
                            @endif
                        </div>
                    </div>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                        <div>
                            <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-bottom: 4px;">Completion Rate</div>
                            <div style="font-size: 24px; font-weight: 700;">{{ $overallCompletion }}%</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-bottom: 4px;">Assignments Graded</div>
                            <div style="font-size: 24px; font-weight: 700;">{{ $totalGraded }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-bottom: 4px;">Total Assignments</div>
                            <div style="font-size: 24px; font-weight: 700;">{{ $totalAssignments }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card assignments-card">
                <div class="assignments-meta">
                    <div>
                        <p class="muted">Pending assignments</p>
                        <h3>Assignments</h3>
                    </div>
                    <span class="pill-count">{{ $pendingAssignments->count() }} pending</span>
                </div>

                @if ($pendingAssignments->isEmpty())
                    <div class="empty-state">
                        You have no pending assignments. You're all caught up!
                    </div>
                @else
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingAssignments as $assignment)
                                    @php
                                        $status = $assignment->computed_status ?? 'Pending';
                                        $statusClass = $status === 'Overdue' ? 'danger' : 'info';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="assignment-title">
                                                <a href="{{ route('assignment.submission', $assignment->id) }}" style="color: inherit; text-decoration: none;">
                                                    {{ $assignment->title }}
                                                </a>
                                            </div>
                                            @if ($assignment->course)
                                                <div class="assignment-course">{{ $assignment->course->course_name }}</div>
                                            @endif
                                        </td>
                                        <td class="due-date">
                                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y g:ia') : 'No due date set' }}
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- My Courses Section -->
            <div class="card" id="courses" style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <p style="color: var(--muted); font-size: 13px; margin-bottom: 4px;">Enrolled courses</p>
                        <h3 style="margin: 0; color: var(--color-primary);">My Courses</h3>
                    </div>
                    <span class="pill-count">{{ $courses->count() }} courses</span>
                </div>

                @if($courses->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        @foreach($courses as $index => $course)
                            @php
                                // Calculate progress based on assignments
                                $totalAssignments = $course->assignments_count ?? 0;
                                $submittedCount = \App\Models\Submissions::whereIn('assignment_id', $course->assignments()->pluck('id'))
                                    ->where('student_id', Auth::id())
                                    ->count();
                                $progress = $totalAssignments > 0 ? min(100, round(($submittedCount / $totalAssignments) * 100)) : 0;
                            @endphp
                            <a href="{{ route('student.course.show', $course->id) }}" style="text-decoration: none; color: inherit;">
                                <div style="background: var(--white); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; height: 200px; display: flex; flex-direction: column;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                                    <div style="height: 120px; position: relative; background: linear-gradient(135deg, var(--color-primary) 0%, #1565C0 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="position: relative; z-index: 1; font-size: 24px; font-weight: 700; color: var(--white); text-shadow: 0 2px 4px rgba(0,0,0,0.3);">{{ $course->course_code }}</div>
                                    </div>
                                    <div style="padding: 16px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div>
                                            <div style="font-size: 16px; font-weight: 600; color: var(--color-primary); margin-bottom: 4px; line-height: 1.4;">{{ strtoupper($course->course_name) }}</div>
                                            <div style="font-size: 13px; color: var(--muted); margin-bottom: 8px;">FACULTY OF COMPUTING</div>
                                        </div>
                                        <div style="font-size: 13px; color: var(--muted);">{{ $progress }}% complete</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p>You are not enrolled in any courses yet.</p>
                    </div>
                @endif
            </div>

            <div class="cards" style="margin-top: 20px;">
                <div class="card" id="grades">
                    <h3>📊 Grades</h3>
                    <p>Track your academic performance. View your grades for all assessments, assignments, and exams.</p>
                </div>
                @if($courses->count() > 0 && ($overallAvgScore ?? null) === null)
                <div class="card" id="progress">
                    <h3>📈 Progress</h3>
                    <p>Monitor your overall academic progress and see personalized recommendations for improvement.</p>
                    <p style="margin-top: 10px; font-size: 13px; color: var(--muted);">Start submitting assignments to see your performance metrics!</p>
                </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        // Auto-hide success alert after 4 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.add('hide');
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 4000);
        }
    </script>
</body>
</html>
