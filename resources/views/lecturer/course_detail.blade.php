<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--color-secondary);
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
        /* Main Content */
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
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #1565C0;
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
        /* Analytics Section */
        .analytics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .analytics-card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .analytics-label {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .analytics-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--color-primary);
        }
        /* Section */
        .section {
            background: var(--white);
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #222;
        }
        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background: var(--color-primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary:hover {
            background: #1565C0;
        }
        .btn-danger {
            background: #E53935;
            color: var(--white);
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger:hover {
            background: #C62828;
        }
        .btn-secondary {
            background: #757575;
            color: var(--white);
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            background: #616161;
        }
        /* Table */
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
            font-size: 14px;
        }
        .assignment-title {
            font-weight: 500;
            color: var(--color-primary);
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
        .badge-secondary {
            background: #f5f5f5;
            color: #616161;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }
        .modal-content {
            background-color: var(--white);
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #222;
        }
        .close {
            color: var(--muted);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
        }
        .close:hover {
            color: #222;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #222;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: var(--font);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
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
            z-index: 1001;
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
        .success-alert.hide {
            animation: slideUp 0.3s ease-out;
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
            .assignments-table {
                font-size: 12px;
            }
            .assignments-table th,
            .assignments-table td {
                padding: 8px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @if (session('success'))
        <div class="success-alert" id="successAlert">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="{{ route('lecturer.courses') }}" class="active">📚 My Courses</a>
            <a href="#students">👥 Students</a>
            <a href="#grades">📊 Grade Management</a>
            <a href="#assignments">✏️ Assignments</a>
            <a href="#analytics">📈 Analytics</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ route('lecturer.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <a href="{{ route('lecturer.courses') }}" class="back-link">
                ← Back to Courses
            </a>

            <div class="course-header">
                <div class="course-code">{{ $course->course_code }}</div>
                <h1 class="course-title">{{ $course->course_name }}</h1>
                <div class="course-info">
                    <div class="info-item">
                        <span>👥</span>
                        <span>{{ $totalStudents }} Students</span>
                    </div>
                    <div class="info-item">
                        <span>📝</span>
                        <span>{{ $assignments->count() }} Assignments</span>
                    </div>
                    <div class="info-item">
                        <span>🏛️</span>
                        <span>FACULTY OF COMPUTING</span>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="analytics">
                <div class="analytics-card">
                    <div class="analytics-label">Total Students</div>
                    <div class="analytics-value">{{ $totalStudents }}</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-label">Pending Grading</div>
                    <div class="analytics-value">{{ $pendingGrading }}</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-label">Completed</div>
                    <div class="analytics-value">{{ $completed }}</div>
                </div>
            </div>

            <!-- Assignments Section -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">Assignments</h2>
                    <button class="btn-primary" onclick="openCreateModal()">+ Create Assignment</button>
                </div>
                @if($assignments->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="assignments-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                    <th>Submissions</th>
                                    <th>Status</th>
                                    <th>Visibility</th>
                                    <th>Grading</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="assignment-title">{{ $assignment->title }}</div>
                                        </td>
                                        <td>
                                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y g:ia') : 'No due date' }}
                                        </td>
                                        <td>{{ $assignment->submissions_count ?? 0 }}</td>
                                        <td>
                                            @if($assignment->status === 'open')
                                                <span class="badge badge-success">Open</span>
                                            @else
                                                <span class="badge badge-danger">Close</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->visibility === 'published')
                                                <span class="badge badge-info">Published</span>
                                            @else
                                                <span class="badge badge-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('lecturer.grading', [$course->id, $assignment->id]) }}" class="btn-secondary">
                                                View ({{ $assignment->submissions_count ?? 0 }})
                                            </a>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-secondary" onclick="openEditModal({{ $assignment->id }}, {{ json_encode($assignment->title) }}, {{ json_encode($assignment->description ?? '') }}, {{ json_encode($assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}, {{ json_encode($assignment->status) }}, {{ json_encode($assignment->visibility) }}, {{ json_encode($assignment->attachment ? basename($assignment->attachment) : '') }}, {{ json_encode($assignment->attachment ?? '') }})">Edit</button>
                                                <form action="{{ route('lecturer.assignment.delete', [$course->id, $assignment->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; color: var(--muted);">
                        <p>No assignments created yet.</p>
                        <button class="btn-primary" onclick="openCreateModal()" style="margin-top: 12px;">Create Assignment</button>
                    </div>
                @endif
            </div>

            <div class="section">
                <h2 class="section-title">Enrolled Students</h2>
                @if($course->students->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                        @foreach($course->students as $student)
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px;">
                                <div style="font-weight: 500; color: #222;">{{ $student->name }}</div>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">{{ $student->email }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; color: var(--muted);">
                        <p>No students enrolled in this course yet.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Create Assignment Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Assignment</h2>
                <button class="close" onclick="closeCreateModal()">&times;</button>
            </div>
            <form action="{{ route('lecturer.assignment.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="title">Title *</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="due_date">Due Date</label>
                    <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="open">Open</option>
                        <option value="close">Close</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="visibility">Visibility *</label>
                    <select class="form-control" id="visibility" name="visibility" required>
                        <option value="hidden">Hidden</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="attachment">Attachment</label>
                    <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Create Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Assignment Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Assignment</h2>
                <button class="close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="edit_title">Title *</label>
                    <input type="text" class="form-control" id="edit_title" name="title" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_description">Description</label>
                    <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_due_date">Due Date</label>
                    <input type="datetime-local" class="form-control" id="edit_due_date" name="due_date">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Status *</label>
                    <select class="form-control" id="edit_status" name="status" required>
                        <option value="open">Open</option>
                        <option value="close">Close</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_visibility">Visibility *</label>
                    <select class="form-control" id="edit_visibility" name="visibility" required>
                        <option value="hidden">Hidden</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_attachment">Attachment</label>
                    <div id="current-attachment" style="margin-bottom: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px; display: none;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 13px; color: var(--muted);">
                                <span style="font-weight: 500;">Current:</span> <span id="current-attachment-name"></span>
                            </span>
                            <a href="#" id="current-attachment-link" target="_blank" style="font-size: 12px; color: var(--color-primary); text-decoration: none;">View</a>
                        </div>
                    </div>
                    <input type="file" class="form-control" id="edit_attachment" name="attachment" accept=".pdf,.doc,.docx,.txt">
                    <small style="display: block; margin-top: 4px; color: var(--muted); font-size: 12px;">Leave empty to keep current attachment, or select a new file to replace it.</small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-hide success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.add('hide');
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 4000);
        }

        // Create Modal Functions
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
            document.querySelector('#createModal form').reset();
        }

        // Edit Modal Functions
        function openEditModal(id, title, description, dueDate, status, visibility, attachmentName, attachmentPath) {
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_due_date').value = dueDate;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_visibility').value = visibility;
            document.getElementById('editForm').action = '{{ route("lecturer.assignment.update", [$course->id, ":id"]) }}'.replace(':id', id);
            
            // Handle attachment display
            const currentAttachmentDiv = document.getElementById('current-attachment');
            const currentAttachmentName = document.getElementById('current-attachment-name');
            const currentAttachmentLink = document.getElementById('current-attachment-link');
            
            if (attachmentPath && attachmentPath !== '' && attachmentPath !== 'null') {
                currentAttachmentDiv.style.display = 'block';
                currentAttachmentName.textContent = attachmentName || 'Current attachment';
                // Build the public URL - attachmentPath is relative to public folder (e.g., assignments/filename.pdf)
                currentAttachmentLink.href = '{{ url("/") }}/' + attachmentPath;
            } else {
                currentAttachmentDiv.style.display = 'none';
                currentAttachmentLink.href = '#';
            }
            
            // Reset file input
            document.getElementById('edit_attachment').value = '';
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editForm').reset();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createModal');
            const editModal = document.getElementById('editModal');
            if (event.target == createModal) {
                closeCreateModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
