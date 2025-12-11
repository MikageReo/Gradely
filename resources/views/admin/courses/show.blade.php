<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->course_code }} - Course Details - GRADELY</title>
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
        .header h1 { font-size: 24px; color: #222; margin-bottom: 4px; }
        .header .course-code { color: var(--muted); font-size: 14px; }
        .section { background: var(--white); padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .section-title { font-size: 20px; font-weight: 600; color: #222; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .btn-primary { background: var(--color-primary); color: var(--white); }
        .btn-primary:hover { background: #B71C1C; }
        .btn-secondary { background: var(--color-secondary); color: var(--white); }
        .btn-secondary:hover { background: #00695C; }
        .btn-danger { background: #d32f2f; color: var(--white); }
        .btn-danger:hover { background: #b71c1c; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .success-alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
        .error-alert { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { font-weight: 600; color: #222; background: #f8f9fa; }
        tbody tr:hover { background: #f8f9fa; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 500; color: #222; font-size: 14px; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: var(--font); }
        .form-control:focus { outline: none; border-color: var(--color-primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; align-items: end; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .badge-info { background: #e3f2fd; color: #1565c0; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-warning { background: #fff3e0; color: #e65100; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: var(--white); margin: 5% auto; padding: 24px; border-radius: 8px; width: 90%; max-width: 500px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { font-size: 20px; font-weight: 600; color: #222; }
        .close { font-size: 28px; font-weight: bold; color: var(--muted); cursor: pointer; border: none; background: none; }
        .close:hover { color: #222; }
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
            @if (session('success'))
                <div class="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-alert">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="header">
                <h1>{{ $course->course_name }}</h1>
                <div class="course-code">{{ $course->course_code }}</div>
            </div>

            <!-- Lecturer Assignments Section -->
            <div class="section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="section-title">Lecturer Assignments</h2>
                    <button class="btn btn-primary" onclick="openLecturerModal()">+ Assign Lecturer</button>
                </div>

                @if($course->courseLecturers->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Lecturer</th>
                                <th>Section</th>
                                <th>Capacity</th>
                                <th>Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->courseLecturers as $cl)
                                <tr>
                                    <td>{{ $cl->lecturer->name }}<br><small style="color: var(--muted);">{{ $cl->lecturer->email }}</small></td>
                                    <td>{{ $cl->section ?? 'Default' }}</td>
                                    <td>{{ $cl->capacity > 0 ? $cl->capacity : 'Unlimited' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $cl->students->count() }} students</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.courses.remove.lecturer', [$course->id, $cl->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will also remove all student enrollments in this section.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: var(--muted); padding: 40px;">No lecturers assigned yet. Click "Assign Lecturer" to get started.</p>
                @endif
            </div>

            <!-- Student Enrollments Section -->
            <div class="section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="section-title">Student Enrollments</h2>
                    <button class="btn btn-primary" onclick="openEnrollmentModal()">+ Enroll Student</button>
                </div>

                @php
                    $allEnrollments = collect();
                    foreach($course->courseLecturers as $cl) {
                        foreach($cl->students as $enrollment) {
                            $allEnrollments->push([
                                'id' => $enrollment->id,
                                'student' => $enrollment->student,
                                'section' => $cl->section ?? 'Default',
                                'lecturer' => $cl->lecturer->name,
                            ]);
                        }
                    }
                @endphp

                @if($allEnrollments->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Section</th>
                                <th>Lecturer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allEnrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment['student']->name }}</td>
                                    <td><small style="color: var(--muted);">{{ $enrollment['student']->email }}</small></td>
                                    <td>{{ $enrollment['section'] }}</td>
                                    <td>{{ $enrollment['lecturer'] }}</td>
                                    <td>
                                        <form action="{{ route('admin.courses.remove.student', [$course->id, $enrollment['id']]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this student from the course?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: var(--muted); padding: 40px;">No students enrolled yet. Click "Enroll Student" to add students.</p>
                @endif
            </div>

            <!-- Assign Lecturer Modal -->
            <div id="lecturerModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Assign Lecturer</h2>
                        <button class="close" onclick="closeLecturerModal()">&times;</button>
                    </div>
                    <form action="{{ route('admin.courses.assign.lecturer', $course->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="lecturer_id">Lecturer *</label>
                            <select class="form-control" id="lecturer_id" name="lecturer_id" required>
                                <option value="">Select a lecturer</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">{{ $lecturer->name }} ({{ $lecturer->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="section">Section</label>
                            <input type="text" class="form-control" id="section" name="section" placeholder="e.g., A, B, Morning">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="capacity">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="0" placeholder="0 for unlimited">
                        </div>
                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            <button type="submit" class="btn btn-primary">Assign</button>
                            <button type="button" class="btn btn-secondary" onclick="closeLecturerModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enroll Student Modal -->
            <div id="enrollmentModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Enroll Student</h2>
                        <button class="close" onclick="closeEnrollmentModal()">&times;</button>
                    </div>
                    <form action="{{ route('admin.courses.enroll.student', $course->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="student_id">Student *</label>
                            <select class="form-control" id="student_id" name="student_id" required>
                                <option value="">Select a student</option>
                                @foreach($allStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="course_lecturer_id">Course Section *</label>
                            <select class="form-control" id="course_lecturer_id" name="course_lecturer_id" required>
                                <option value="">Select a section</option>
                                @foreach($course->courseLecturers as $cl)
                                    <option value="{{ $cl->id }}">
                                        {{ $cl->lecturer->name }} - {{ $cl->section ?? 'Default' }}
                                        ({{ $cl->students->count() }}/{{ $cl->capacity > 0 ? $cl->capacity : '∞' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            <button type="submit" class="btn btn-primary">Enroll</button>
                            <button type="button" class="btn btn-secondary" onclick="closeEnrollmentModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openLecturerModal() {
            document.getElementById('lecturerModal').style.display = 'block';
        }
        function closeLecturerModal() {
            document.getElementById('lecturerModal').style.display = 'none';
        }
        function openEnrollmentModal() {
            document.getElementById('enrollmentModal').style.display = 'block';
        }
        function closeEnrollmentModal() {
            document.getElementById('enrollmentModal').style.display = 'none';
        }
        window.onclick = function(event) {
            const lecturerModal = document.getElementById('lecturerModal');
            const enrollmentModal = document.getElementById('enrollmentModal');
            if (event.target == lecturerModal) {
                lecturerModal.style.display = 'none';
            }
            if (event.target == enrollmentModal) {
                enrollmentModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

