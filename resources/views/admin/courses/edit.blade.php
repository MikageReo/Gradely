<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Course - GRADELY</title>
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
        .sidebar a:hover { background: rgba(255,255,255,0.1); }
        .sidebar .logout { background: rgba(255,0,0,0.3); margin-top: 30px; }
        .sidebar .logout:hover { background: rgba(255,0,0,0.5); }
        .main-content { flex: 1; padding: 30px; }
        .header { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .header h1 { font-size: 24px; color: #222; margin-bottom: 8px; }
        .header p { color: var(--muted); font-size: 14px; }
        .form-container { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #222; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: var(--font); }
        .form-control:focus { outline: none; border-color: var(--color-primary); }
        .error { color: #d32f2f; font-size: 12px; margin-top: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .btn-primary { background: var(--color-primary); color: var(--white); }
        .btn-primary:hover { background: #B71C1C; }
        .btn-secondary { background: #6c757d; color: var(--white); }
        .btn-secondary:hover { background: #5a6268; }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('admin.new_student_registration') }}">👤 Register Student</a>
            <a href="{{ route('admin.new_lecturer_registration') }}">👨‍🏫 Register Lecturer</a>
            <a href="{{ route('admin.courses.index') }}">📚 Manage Courses</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Edit Course</h1>
                <p>Update course information.</p>
            </div>

            <div class="form-container">
                <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label class="form-label" for="course_code">Course Code *</label>
                        <input type="text" class="form-control @error('course_code') border: 1px solid #d32f2f; @enderror" 
                               id="course_code" name="course_code" 
                               value="{{ old('course_code', $course->course_code) }}" required>
                        @error('course_code')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="course_name">Course Name *</label>
                        <input type="text" class="form-control @error('course_name') border: 1px solid #d32f2f; @enderror" 
                               id="course_name" name="course_name" 
                               value="{{ old('course_name', $course->course_name) }}" required>
                        @error('course_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Course</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

