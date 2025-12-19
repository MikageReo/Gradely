@extends('lecturer.layout')

@section('title', $course->course_code . ' - ' . $course->course_name . ' - GRADELY')

@push('styles')
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            color: white;
        }
        .analytics-label {
            font-size: 13px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 8px;
        }
        .analytics-value {
            font-size: 32px;
            font-weight: 700;
            color: white;
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
        /* Assignments List */
        .assignments-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .assignment-card {
            background: var(--white);
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .assignment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--color-secondary);
            transition: width 0.3s ease;
        }
        .assignment-card:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: var(--color-secondary);
        }
        .assignment-card:hover::before {
            width: 6px;
        }
        .assignment-card-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 200px;
        }
        .assignment-title {
            font-weight: 600;
            color: #222;
            font-size: 16px;
            line-height: 1.4;
            margin: 0;
            flex: 1;
        }
        .assignment-badges {
            display: flex;
            flex-direction: row;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
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
        .assignment-meta {
            display: flex;
            flex-direction: row;
            gap: 20px;
            flex-wrap: wrap;
            flex: 1;
            min-width: 250px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
            white-space: nowrap;
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
        .submissions-info {
            background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%);
            border-radius: 8px;
            padding: 12px 16px;
            text-align: center;
            min-width: 100px;
            flex-shrink: 0;
        }
        .submissions-label {
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .submissions-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-secondary);
            line-height: 1.2;
        }
        .submissions-text {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }
        .assignment-actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            flex-shrink: 0;
        }
        .btn-action {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            white-space: nowrap;
        }
        .btn-grade {
            background: var(--color-secondary);
            color: var(--white);
        }
        .btn-grade:hover {
            background: #00695C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 137, 123, 0.3);
        }
        .btn-edit {
            background: var(--color-primary);
            color: var(--white);
        }
        .btn-edit:hover {
            background: #1565C0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }
        .btn-delete {
            background: #E53935;
            color: var(--white);
        }
        .btn-delete:hover {
            background: #C62828;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 57, 53, 0.3);
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
            border: 2px solid #999;
            border-radius: 6px;
            font-size: 14px;
            font-family: var(--font);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            border-width: 2px;
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
        /* Simple Pagination Styles */
        .pagination-wrapper {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        .simple-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            min-width: 40px;
            height: 36px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 2px solid var(--color-primary);
            background: var(--color-primary);
            color: var(--white);
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(25, 118, 210, 0.15);
        }
        .pagination-btn:hover:not(.disabled) {
            background: #1565C0;
            border-color: #1565C0;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
        }
        .pagination-btn:active:not(.disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(25, 118, 210, 0.2);
        }
        .pagination-btn.disabled {
            background: #e0e0e0;
            color: #999;
            border-color: #d0d0d0;
            cursor: not-allowed;
            opacity: 0.5;
            box-shadow: none;
        }
        .pagination-btn.disabled:hover {
            transform: none;
            box-shadow: none;
            background: #e0e0e0;
            border-color: #d0d0d0;
        }
        .pagination-info {
            color: var(--muted);
            font-size: 14px;
            white-space: nowrap;
        }
        .pagination-info strong {
            color: #222;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .assignment-card {
                flex-direction: column;
                align-items: stretch;
            }
            .assignment-card-header {
                min-width: auto;
            }
            .assignment-meta {
                flex-direction: column;
                gap: 12px;
                min-width: auto;
            }
            .meta-item {
                white-space: normal;
            }
            .assignment-actions {
                flex-direction: column;
            }
            .btn-action {
                width: 100%;
            }
            .simple-pagination {
                gap: 12px;
                flex-wrap: wrap;
            }
            .pagination-btn {
                padding: 6px 12px;
                min-width: 36px;
                height: 32px;
                font-size: 13px;
            }
            .pagination-info {
                font-size: 13px;
            }
        }
    </style>
@endpush

@section('content')
            <a href="{{ route('lecturer.dashboard') }}" class="back-link">
                ← Back to Dashboard
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
                    <a class="btn-primary" href="{{ route('lecturer.assignment.create', $course->id) }}">+ Create Assignment</a>
                </div>

                <!-- Search and Filter Form (Functional Appropriateness) -->
                <form method="GET" action="{{ route('lecturer.course.show', $course->id) }}" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label for="search" style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted);">Search</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                               placeholder="Search by title..."
                               class="form-control" style="width: 100%;">
                    </div>
                    <div style="min-width: 150px;">
                        <label for="status" style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted);">Status</label>
                        <select id="status" name="status" class="form-control" style="width: 100%;">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="close" {{ request('status') === 'close' ? 'selected' : '' }}>Close</option>
                        </select>
                    </div>
                    <div style="min-width: 150px;">
                        <label for="visibility" style="display: block; margin-bottom: 6px; font-size: 13px; color: var(--muted);">Visibility</label>
                        <select id="visibility" name="visibility" class="form-control" style="width: 100%;">
                            <option value="">All Visibility</option>
                            <option value="published" {{ request('visibility') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="hidden" {{ request('visibility') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn-primary" style="height: 38px;">Filter</button>
                    </div>
                    @if(request('search') || request('status') || request('visibility'))
                        <div>
                            <a href="{{ route('lecturer.course.show', $course->id) }}" class="btn-secondary" style="height: 38px; display: inline-block; line-height: 38px;">Clear</a>
                        </div>
                    @endif
                </form>

                @if($assignments->count() > 0)
                    <div class="assignments-list">
                        @foreach($assignments as $assignment)
                            <div class="assignment-card">
                                <div class="assignment-card-header">
                                    <h3 class="assignment-title">{{ $assignment->title }}</h3>
                                    <div class="assignment-badges">
                                        @if($assignment->status === 'open')
                                            <span class="badge badge-success">✓ Open</span>
                                        @else
                                            <span class="badge badge-danger">✗ Closed</span>
                                        @endif
                                        @if($assignment->visibility === 'published')
                                            <span class="badge badge-info">📢 Published</span>
                                        @else
                                            <span class="badge badge-secondary">🔒 Hidden</span>
                                        @endif
                                    </div>
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
                                                    <span style="color: #c62828;">Overdue {{ $assignment->due_date->diffForHumans() }}</span>
                                                @else
                                                    <span style="color: #2e7d32;">Due in {{ $assignment->due_date->diffForHumans() }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="submissions-info">
                                    <div class="submissions-label">Submissions</div>
                                    <div class="submissions-value">{{ $assignment->submissions_count ?? 0 }}/{{ $totalStudents }}</div>
                                    <div class="submissions-text">
                                        @if(($assignment->submissions_count ?? 0) > 0)
                                            {{ $assignment->submissions_count }} student{{ $assignment->submissions_count > 1 ? 's' : '' }} submitted
                                        @else
                                            No submissions yet
                                        @endif
                                    </div>
                                </div>

                                <div class="assignment-actions">
                                    <a href="{{ route('lecturer.grading', [$course->id, $assignment->id]) }}" class="btn-action btn-grade">
                                        📊 Grade ({{ $assignment->pending_grading_count ?? 0 }})
                                    </a>
                                    <a href="{{ route('lecturer.assignment.edit', [$course->id, $assignment->id]) }}" class="btn-action btn-edit">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('lecturer.assignment.delete', [$course->id, $assignment->id]) }}" method="POST" style="display: flex;" onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination (Capacity improvement) -->
                    <div class="pagination-wrapper">
                        <div class="simple-pagination">
                            <a href="{{ $assignments->previousPageUrl() }}"
                               class="pagination-btn {{ $assignments->onFirstPage() ? 'disabled' : '' }}"
                               {{ $assignments->onFirstPage() ? 'onclick="return false;"' : '' }}>
                                &lt;
                            </a>
                            <span class="pagination-info">
                                Showing <strong>{{ $assignments->firstItem() ?? 0 }}</strong> to <strong>{{ $assignments->lastItem() ?? 0 }}</strong> of <strong>{{ $assignments->total() }}</strong> assignments
                            </span>
                            <a href="{{ $assignments->nextPageUrl() }}"
                               class="pagination-btn {{ !$assignments->hasMorePages() ? 'disabled' : '' }}"
                               {{ !$assignments->hasMorePages() ? 'onclick="return false;"' : '' }}>
                                &gt;
                            </a>
                        </div>
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

            <!-- Modals removed in favor of dedicated pages -->
@endsection

@push('scripts')
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
</script>
@endpush
