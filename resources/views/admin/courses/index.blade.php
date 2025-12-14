<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Courses - GRADELY</title>
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
        .sidebar h2 { font-size: 18px; margin-bottom: 30px; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 10px; }
        .sidebar a { display: block; color: var(--white); text-decoration: none; padding: 10px 12px; margin: 8px 0; border-radius: 6px; transition: background 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); }
        .sidebar .logout { background: rgba(255,0,0,0.3); margin-top: 30px; }
        .sidebar .logout:hover { background: rgba(255,0,0,0.5); }
        .main-content { flex: 1; padding: 30px; }
        .header { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; color: #222; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .btn-primary { background: var(--color-primary); color: var(--white); }
        .btn-primary:hover { background: #B71C1C; }
        .btn-secondary { background: var(--color-secondary); color: var(--white); }
        .btn-secondary:hover { background: #00695C; }
        .btn-danger { background: #d32f2f; color: var(--white); }
        .btn-danger:hover { background: #b71c1c; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .success-alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
        .table-container { background: var(--white); border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f8f9fa; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { font-weight: 600; color: #222; }
        tbody tr:hover { background: #f8f9fa; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .badge-info { background: #e3f2fd; color: #1565c0; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .actions { display: flex; gap: 8px; }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.5; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('admin.new_student_registration') }}">👤 Register Student</a>
            <a href="{{ route('admin.new_lecturer_registration') }}">👨‍🏫 Register Lecturer</a>
            <a href="{{ route('admin.courses.index') }}" class="active">📚 Manage Courses</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <main class="main-content">
            @if (session('success'))
                <div class="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="header">
                <h1>Manage Courses</h1>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">+ Create New Course</a>
            </div>

            <div class="table-container">
                @if($courses->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Lecturers</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td><strong>{{ $course->course_code }}</strong></td>
                                    <td>{{ $course->course_name }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $course->course_lecturers_count }} Lecturer(s)</span>
                                        @if($course->courseLecturers->count() > 0)
                                            <div style="margin-top: 4px; font-size: 12px; color: var(--muted);">
                                                @foreach($course->courseLecturers->take(2) as $cl)
                                                    {{ $cl->lecturer->name }}{{ $cl->section ? ' (' . $cl->section . ')' : '' }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                @if($course->courseLecturers->count() > 2)
                                                    <span>+{{ $course->courseLecturers->count() - 2 }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-success">{{ $course->total_students ?? 0 }} Student(s)</span></td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-secondary btn-sm">View</a>
                                            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course? This will also delete all assignments and enrollments.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <h3>No courses yet</h3>
                        <p style="margin-top: 8px;">Create your first course to get started.</p>
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary" style="margin-top: 16px;">Create Course</a>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>

