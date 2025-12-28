<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assignment: {{ $assignment->title }} - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --danger: #E53935;
            --success: #43A047;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            min-height: 100vh;
            margin: 0;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (student-style navigation) */
        .sidebar {
            width: 250px;
            background: var(--color-primary);
            color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }

        /* Lecturer sidebar uses secondary color (teal) */
        .sidebar.lecturer-sidebar {
            background: var(--color-secondary);
        }

        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
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

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar .logout {
            background: rgba(255, 0, 0, 0.3);
            margin-top: 30px;
        }

        .sidebar .logout:hover {
            background: rgba(255, 0, 0, 0.5);
        }

        .main-content {
            flex: 1;
            padding: 24px;
        }

        .assignment-card {
            background: var(--white);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .assignment-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .assignment-icon {
            width: 48px;
            height: 48px;
            background: var(--color-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--white);
        }

        .assignment-title {
            font-size: 24px;
            font-weight: 600;
            color: #222;
        }

        .assignment-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-icon {
            width: 20px;
            height: 20px;
            color: var(--color-primary);
        }

        .info-label {
            font-size: 14px;
            color: var(--muted);
            margin-right: 8px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: #222;
        }

        .info-value.danger {
            color: var(--danger);
        }

        .info-value.success {
            color: var(--success);
        }

        .upload-section {
            border: 2px dashed #BBDEFB;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            background: #F5F5F5;
        }

        .upload-section.has-files {
            border-color: var(--color-primary);
            background: #E3F2FD;
        }

        .file-input-wrapper {
            margin-bottom: 15px;
        }

        .file-input {
            display: none;
        }

        .file-input-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--color-primary);
            color: var(--white);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }

        .file-input-label:hover {
            background: #1565C0;
        }

        .file-list {
            margin-top: 15px;
            text-align: left;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: var(--white);
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .file-name {
            font-size: 14px;
            color: #222;
        }

        .file-remove {
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 12px;
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--color-primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: #1565C0;
        }

        .submit-btn:disabled {
            background: #BBDEFB;
            cursor: not-allowed;
        }

        .comments-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #E0E0E0;
        }

        .comments-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .comments-icon {
            width: 24px;
            height: 24px;
            color: var(--color-primary);
        }

        .comments-title {
            font-size: 18px;
            font-weight: 600;
            color: #222;
        }

        .comments-subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        .comment-list {
            margin-bottom: 20px;
        }

        .comment-item {
            padding: 12px;
            margin-bottom: 12px;
            background: #F5F5F5;
            border-radius: 6px;
        }

        .comment-author {
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 4px;
        }

        .comment-role-badge {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 11px;
            background: #e3f2fd;
            color: #1565c0;
        }

        .comment-text {
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }

        .comment-form {
            margin-top: 15px;
        }

        .comment-input-container {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .comment-input {
            flex: 1;
            padding: 12px;
            border: 1px solid #E0E0E0;
            border-radius: 6px;
            font-size: 14px;
            font-family: var(--font);
        }

        .comment-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            background: var(--color-primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .comment-submit:hover {
            background: #1565C0;
        }

        .history-btn {
            margin-left: auto;
            padding: 8px 16px;
            background: var(--color-secondary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .history-btn:hover {
            background: #00695C;
        }

        .comment-time {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: var(--white);
            margin: 30px auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
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
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            color: #222;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            max-height: calc(90vh - 200px);
        }

        .modal-comment-list {
            margin-bottom: 20px;
        }

        .modal-comment-item {
            padding: 12px;
            margin-bottom: 12px;
            background: #F5F5F5;
            border-radius: 6px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 2px solid #f0f0f0;
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 600;
            color: #1f2933;
        }

        .page-breadcrumb {
            font-size: 13px;
            color: var(--muted);
        }

        .submitted-files {
            margin-top: 15px;
            padding: 15px;
            background: #E8F5E9;
            border-radius: 6px;
        }

        .submitted-files-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2E7D32;
        }

        .submitted-file-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            color: #1B5E20;
        }

        .submitted-file-item a {
            margin-left: auto;
            color: var(--color-primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }

        .submitted-file-item a:hover {
            text-decoration: underline;
        }

        .file-edit-btn, .file-delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: var(--muted);
        }

        .file-edit-btn:hover {
            background: #E3F2FD;
            color: var(--color-primary);
        }

        .file-delete-btn:hover {
            background: #FFEBEE;
            color: #E53935;
        }

        .assignment-files {
            margin-top: 20px;
            padding: 15px;
            background: #E3F2FD;
            border-radius: 6px;
            border: 1px solid #BBDEFB;
        }

        .assignment-files-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #1565C0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .assignment-file-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--white);
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .assignment-file-item a {
            margin-left: auto;
            color: var(--color-primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 4px 12px;
            background: #E3F2FD;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .assignment-file-item a:hover {
            background: #BBDEFB;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .main-content {
                padding: 16px;
            }

            .assignment-info {
                grid-template-columns: 1fr;
            }

            .comment-input-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    @php
        $role = Auth::user()->role ?? 'student';
        $dashboardUrl =
            $role === 'student'
                ? route('student.dashboard')
                : ($role === 'lecturer'
                    ? route('lecturer.dashboard')
                    : route('admin.dashboard'));
    @endphp
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar {{ Auth::user()->role === 'lecturer' ? 'lecturer-sidebar' : '' }}">
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
            <a href="{{ $dashboardUrl }}">🏠 Dashboard</a>
            @if ($role === 'student')
                <a href="{{ route('student.dashboard') }}#courses">📚 My Courses</a>
                @if (isset($assignment->course))
                    <a href="{{ route('student.course.show', $assignment->course->id) }}">📘 This Course</a>
                @endif
            @elseif($role === 'lecturer')
                <a href="{{ route('lecturer.courses') }}">📚 My Courses</a>
                @if (isset($assignment->course))
                    <a href="{{ route('lecturer.course.show', $assignment->course->id) }}">📘 This Course</a>
                @endif
            @endif
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">Assignment Submission</div>
                    <div class="page-breadcrumb">
                        {{ $assignment->course->course_code ?? '' }} ·
                        {{ $assignment->course->course_name ?? 'Course' }}
                    </div>
                </div>
                <div style="font-size: 13px; color: var(--muted);">
                    Due: {{ $assignment->due_date ? $assignment->due_date->format('M d, Y g:ia') : 'No due date' }}
                </div>
            </div>

            <div class="assignment-card">
                <div class="assignment-header">
                    <div class="assignment-icon">📝</div>
                    <h1 class="assignment-title">Assignment: {{ $assignment->title }}</h1>
                </div>

                <div class="assignment-info">
                    <div class="info-item">
                        <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="info-label">Due Date:</span>
                        <span
                            class="info-value">{{ $assignment->due_date ? $assignment->due_date->format('d M Y') : 'No due date' }}</span>
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="info-label">Submission Status:</span>
                        @if ($submission)
                            <span class="info-value success">✓ Submitted</span>
                        @else
                            <span class="info-value danger">× Not Submitted</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="info-label">Time Remaining:</span>
                        @php
                            $isExpired = $assignment->due_date && $assignment->due_date->isPast();
                        @endphp
                        @if ($isExpired)
                            <span class="info-value danger">Expired</span>
                        @elseif($assignment->due_date)
                            <span class="info-value">{{ now()->diffForHumans($assignment->due_date, true) }}
                                remaining</span>
                        @else
                            <span class="info-value">No deadline</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="info-label">Grading Status:</span>
                        @if ($submission && $submission->status === 'marked')
                            <span class="info-value success">✓ Graded</span>
                        @else
                            <span class="info-value danger">✗ Not Graded</span>
                        @endif
                    </div>
                </div>

                @if ($assignment->assignmentFiles && $assignment->assignmentFiles->count() > 0)
                    <div class="assignment-files">
                        <div class="assignment-files-title">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Assignment Files:
                        </div>
                        @foreach($assignment->assignmentFiles as $file)
                            <div class="assignment-file-item">
                                <span>📎</span>
                                <span>{{ $file->original_filename }}</span>
                                <a href="{{ url('/' . $file->file_path) }}" target="_blank">View/Download</a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($assignment->description)
                    <div style="margin-top: 20px; padding: 15px; background: #F5F5F5; border-radius: 6px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: #222;">Description
                        </h3>
                        <p style="color: #333; line-height: 1.6; white-space: pre-wrap;">{{ $assignment->description }}
                        </p>
                    </div>
                @endif

                @if ($submission && $submission->submissionFiles->count() > 0)
                    <div class="submitted-files">
                        <div class="submitted-files-title">
                            Submitted Files:
                            @if (Auth::user()->role === 'lecturer' && $submission->student)
                                <span style="font-weight: normal; color: #666;">(by
                                    {{ $submission->student->name }})</span>
                            @endif
                        </div>
                        @foreach ($submission->submissionFiles as $file)
                            <div class="submitted-file-item" id="file-item-{{ $file->id }}">
                                <span>📄</span>
                                <span>{{ $file->original_filename }}</span>
                                <div style="margin-left: auto; display: flex; gap: 8px; align-items: center;">
                                    <a href="{{ route('submission.file.download', ['submissionId' => $submission->id, 'fileId' => $file->id]) }}"
                                        target="_blank"
                                        style="color: var(--color-primary); text-decoration: none; font-size: 12px; font-weight: 500;">View/Download</a>

                                    @if (Auth::user()->role === 'student' && $submission->student_id === Auth::id() && ($submission->status !== 'marked' && $submission->score === null))
                                        <button type="button"
                                            class="file-edit-btn"
                                            onclick="editFile({{ $file->id }}, '{{ $file->original_filename }}')"
                                            title="Replace file">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                            class="file-delete-btn"
                                            onclick="deleteFile({{ $file->id }}, '{{ $file->original_filename }}')"
                                            title="Delete file">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (Auth::user()->role === 'lecturer' && $submission)
                    <div
                        style="margin-top: 30px; padding: 20px; background: #FFF9E6; border-radius: 8px; border: 2px solid #FFD54F;">
                        <h3 style="margin-bottom: 15px; color: #F57C00; font-size: 18px;">📝 Grade Submission</h3>
                        <form id="gradeForm" action="{{ route('assignment.submission.grade', $assignment->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="submission_id" value="{{ $submission->id }}">
                            <input type="hidden" name="student_id" value="{{ $submission->student_id }}">
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #222;">Score
                                    (0-100)</label>
                                <input id="scoreInput" type="number" name="score"
                                    value="{{ $submission->score ?? '' }}" min="0" max="100"
                                    step="0.01"
                                    style="width: 100%; max-width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label
                                    style="display: block; margin-bottom: 5px; font-weight: 500; color: #222;">Feedback</label>
                                <textarea id="feedbackInput" name="lecturer_feedback" rows="4"
                                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: var(--font);">{{ $submission->lecturer_feedback ?? '' }}</textarea>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #222; font-size: 14px;">Upload Feedback Files</label>
                                <div class="upload-section" id="feedbackUploadSection">
                                    <div class="file-input-wrapper">
                                        <input type="file" name="feedback_files[]" id="feedbackFilesInput" class="file-input" multiple
                                            accept=".pdf,.doc,.docx,.txt,.rtf,.odt,.jpg,.jpeg,.png,.gif">
                                        <label for="feedbackFilesInput" class="file-input-label">
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                                <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                                            </svg>
                                            Choose feedback files to upload
                                        </label>
                                        <div style="margin-top: 8px; font-size: 12px; color: var(--muted); text-align: center;">
                                            Maximum file size: 20MB per file (PDF, DOC, DOCX, TXT, RTF, ODT, JPG, JPEG, PNG, GIF)
                                        </div>
                                    </div>
                                    <div class="file-list" id="feedbackFileList"></div>
                                </div>
                            </div>
                            @if($submission->feedbackFiles && $submission->feedbackFiles->count() > 0)
                                <div style="margin-bottom: 15px; padding: 15px; background: #f5f5f5; border-radius: 8px; border: 1px solid #e0e0e0;">
                                    <strong style="display: block; margin-bottom: 10px; color: #222; font-size: 14px;">Previously Uploaded Feedback Files:</strong>
                                    <div id="existingFeedbackFilesList">
                                        @foreach($submission->feedbackFiles as $file)
                                            <div class="file-item" style="background: white;">
                                                <div style="flex: 1;">
                                                    <a href="{{ route('feedback.file.download', ['submissionId' => $submission->id, 'fileId' => $file->id]) }}" 
                                                       target="_blank"
                                                       class="file-name" style="color: var(--color-primary); text-decoration: none;">
                                                        📎 {{ $file->original_filename }}
                                                    </a>
                                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                                        {{ number_format($file->file_size / 1024, 2) }} KB
                                                    </div>
                                                </div>
                                                <button type="button" class="file-remove delete-feedback-file" 
                                                        data-file-id="{{ $file->id }}" 
                                                        data-submission-id="{{ $submission->id }}">
                                                    Remove
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <button type="submit"
                                style="padding: 10px 20px; background: #FF9800; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">Save
                                Grade & Feedback</button>
                        </form>
                        <div id="gradeDisplay"
                            style="margin-top: 15px; padding: 10px; background: #E8F5E9; border-radius: 4px; {{ $submission->score === null ? 'display: none;' : '' }}">
                            <strong>Current Grade:</strong> <span
                                id="gradeScore">{{ $submission->score ?? '' }}</span> / 100
                            @if ($submission->grade)
                                <strong style="margin-left: 15px;">Letter Grade:</strong> <span id="gradeLetter"
                                    style="font-size: 18px; font-weight: bold; color: var(--color-primary);">{{ $submission->grade }}</span>
                            @else
                                <strong style="margin-left: 15px;">Letter Grade:</strong> <span id="gradeLetter"
                                    style="font-size: 18px; font-weight: bold; color: var(--color-primary);"></span>
                            @endif
                            <div id="markedAt" style="margin-top: 5px;">
                                @if ($submission->marked_at)
                                    <small style="color: var(--muted);">Marked on:
                                        {{ $submission->marked_at->format('M d, Y g:ia') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if (Auth::user()->role === 'student' && $submission && $submission->status === 'marked')
                    <div
                        style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); border-radius: 8px; border: 2px solid #4CAF50;">
                        <h3
                            style="margin-bottom: 15px; color: #2E7D32; font-size: 20px; display: flex; align-items: center; gap: 10px;">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Your Grade
                        </h3>
                        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                            <div
                                style="padding: 15px; background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div style="font-size: 14px; color: var(--muted); margin-bottom: 5px;">Score</div>
                                <div style="font-size: 28px; font-weight: bold; color: #2E7D32;">
                                    {{ $submission->score ?? 'N/A' }} / 100
                                </div>
                            </div>
                            @if ($submission->grade)
                                <div
                                    style="padding: 15px; background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 5px;">Letter Grade
                                    </div>
                                    <div style="font-size: 36px; font-weight: bold; color: var(--color-primary);">
                                        {{ $submission->grade }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if ($submission->marked_at)
                            <div style="margin-top: 15px; font-size: 14px; color: var(--muted);">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"
                                    style="vertical-align: middle; margin-right: 5px;">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                Marked on: {{ $submission->marked_at->format('M d, Y g:ia') }}
                            </div>
                        @endif
                        <div id="feedbackDisplay"
                            style="margin-top: 20px; padding: 15px; background: white; border-radius: 6px; border-left: 4px solid var(--color-primary); {{ !$submission->lecturer_feedback && (!$submission->feedbackFiles || $submission->feedbackFiles->count() == 0) ? 'display: none;' : '' }}">
                            <div style="font-weight: 600; margin-bottom: 8px; color: #222; font-size: 16px;">💬 Lecturer Feedback:</div>
                            @if($submission->lecturer_feedback)
                                <div id="feedbackText" style="color: #333; line-height: 1.6; white-space: pre-wrap; margin-bottom: {{ ($submission->feedbackFiles && $submission->feedbackFiles->count() > 0) ? '15px' : '0' }};">
                                    {{ $submission->lecturer_feedback }}
                                </div>
                            @else
                                <div id="feedbackText" style="color: #999; font-style: italic; margin-bottom: {{ ($submission->feedbackFiles && $submission->feedbackFiles->count() > 0) ? '15px' : '0' }};">
                                    No text feedback provided.
                                </div>
                            @endif
                            @if($submission->feedbackFiles && $submission->feedbackFiles->count() > 0)
                                <div id="feedbackFilesDisplay" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                    <strong style="display: block; margin-bottom: 10px; color: #222; font-size: 15px;">📁 Feedback Files:</strong>
                                    <div id="feedbackFilesList">
                                        @foreach($submission->feedbackFiles as $file)
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f5f5f5; border-radius: 4px; margin-bottom: 8px; border: 1px solid #e0e0e0;">
                                                <div style="display: flex; align-items: center; flex: 1; gap: 10px;">
                                                    <span style="font-size: 20px;">📎</span>
                                                    <div style="flex: 1;">
                                                        <a href="{{ route('feedback.file.download', ['submissionId' => $submission->id, 'fileId' => $file->id]) }}" 
                                                           target="_blank"
                                                           style="color: var(--color-primary); text-decoration: none; font-weight: 500; display: block; margin-bottom: 2px;">
                                                            {{ $file->original_filename }}
                                                        </a>
                                                        <small style="color: var(--muted); font-size: 12px;">
                                                            {{ number_format($file->file_size / 1024, 2) }} KB
                                                        </small>
                                                    </div>
                                                </div>
                                                <a href="{{ route('feedback.file.download', ['submissionId' => $submission->id, 'fileId' => $file->id]) }}" 
                                                   target="_blank"
                                                   style="padding: 6px 12px; background: var(--color-primary); color: white; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 500; white-space: nowrap;">
                                                    View/Download
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (Auth::user()->role === 'student' && (!$submission || $submission->status !== 'marked'))
                    <form id="submissionForm" action="{{ route('assignment.submission.store', $assignment->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-section" id="uploadSection">
                            <div class="file-input-wrapper">
                                <input type="file" name="files[]" id="fileInput" class="file-input" multiple
                                    accept=".pdf,.doc,.docx,.txt,.rtf,.odt">
                                <label for="fileInput" class="file-input-label">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                                    </svg>
                                    Choose files to upload
                                </label>
                                <div style="margin-top: 8px; font-size: 12px; color: var(--muted); text-align: center;">
                                    Maximum file size: 20MB per file (PDF, DOC, DOCX, TXT, RTF, ODT)
                                </div>
                            </div>
                            <div class="file-list" id="fileList"></div>
                            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                                Submit
                            </button>
                        </div>
                    </form>
                @endif

                @if (Auth::user()->role === 'lecturer' && !$submission)
                    <div class="upload-section" style="background: #FFF3E0; border-color: #FFB74D;">
                        <p style="color: #E65100;">No submissions yet for this assignment.</p>
                    </div>
                @endif

                <div class="comments-section">
                    <div class="comments-header">
                        <svg class="comments-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h2 class="comments-title">Questions & Feedback</h2>
                            <div class="comments-subtitle">
                                @if (Auth::user()->role === 'student')
                                    Ask your lecturer questions or share notes about this assignment. Only you and your
                                    lecturer can see this conversation.
                                @else
                                    Answer the student's questions and guide them on this assignment. Only you and this
                                    student can see this conversation.
                                @endif
                            </div>
                        </div>
                        @if ($submission && $submission->submissionComments->count() >= 2)
                            <button class="history-btn" onclick="openHistoryModal()">History</button>
                        @endif
                    </div>

                    <div class="comment-list">
                        @if ($submission && $submission->submissionComments->count() > 0)
                            @php
                                $latestComments = $submission->submissionComments->take(2);
                            @endphp
                            @foreach ($latestComments as $comment)
                                <div class="comment-item">
                                    <div class="comment-author">
                                        {{ $comment->user->role === 'lecturer' ? 'Lecturer' : (Auth::id() === $comment->user->id ? 'You' : 'Student') }}:
                                        {{ $comment->user->name }}
                                        <span class="comment-role-badge">
                                            {{ ucfirst($comment->user->role) }}
                                        </span>
                                    </div>
                                    <div class="comment-text">{{ $comment->comment }}</div>
                                    <div class="comment-time">{{ $comment->created_at->format('M d, Y g:ia') }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="comment-item">
                                <div class="comment-text" style="color: var(--muted);">
                                    No comments yet.
                                    @if (Auth::user()->role === 'student')
                                        Use the box below to ask your first question about this assignment.
                                    @else
                                        Once the student comments here, you can reply to support them.
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($submission || Auth::user()->role === 'student')
                        <form id="commentForm" action="{{ route('assignment.submission.comment', $assignment->id) }}"
                            method="POST" class="comment-form" data-current-user-name="{{ Auth::user()->name }}"
                            data-current-user-id="{{ Auth::id() }}">
                            @csrf
                            @if (Auth::user()->role === 'lecturer' && isset($submission))
                                <input type="hidden" name="student_id" value="{{ $submission->student_id }}">
                            @endif
                            <div class="comment-input-container">
                                <input id="commentInput" type="text" name="comment"
                                    placeholder="Type your comment..." class="comment-input" required>
                                <button type="submit" class="comment-submit">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="comment-item" style="background: #FFF3E0; color: #E65100;">
                            <div class="comment-text">No submission available. Comments can be added once a student
                                submits their assignment.</div>
                        </div>
                    @endif
                </div>
            </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Comment History</h2>
                <button class="close" onclick="closeHistoryModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-comment-list">
                    @if ($submission && $submission->submissionComments->count() > 0)
                        @foreach ($submission->submissionComments as $comment)
                            <div class="modal-comment-item">
                                <div class="comment-author">
                                    {{ $comment->user->role === 'lecturer' ? 'Lecturer' : (Auth::user()->id === $comment->user->id ? 'You' : $comment->user->name) }}:
                                    {{ $comment->user->name }}
                                </div>
                                <div class="comment-text">{{ $comment->comment }}</div>
                                <div class="comment-time">{{ $comment->created_at->format('M d, Y g:ia') }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="modal-comment-item">
                            <div class="comment-text" style="color: var(--muted);">No comments yet. Start the
                                conversation!</div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                @if ($submission || Auth::user()->role === 'student')
                    <form action="{{ route('assignment.submission.comment', $assignment->id) }}" method="POST"
                        class="comment-form">
                        @csrf
                        <div class="comment-input-container">
                            <input type="text" name="comment" placeholder="Type your comment..."
                                class="comment-input" required>
                            <button type="submit" class="comment-submit">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
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

        // File upload handling
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const submitBtn = document.getElementById('submitBtn');
        const uploadSection = document.getElementById('uploadSection');
        const selectedFiles = [];

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                const maxSize = 20 * 1024 * 1024; // 20MB in bytes
                const invalidFiles = [];
                const validFiles = [];

                // Validate file sizes
                files.forEach(file => {
                    if (file.size > maxSize) {
                        invalidFiles.push(file.name);
                    } else {
                        validFiles.push(file);
                    }
                });

                // Show error for files that are too large
                if (invalidFiles.length > 0) {
                    alert(`The following files exceed the 20MB limit:\n${invalidFiles.join('\n')}\n\nPlease select smaller files.`);
                }

                // Update selectedFiles with valid files
                selectedFiles.length = 0;
                selectedFiles.push(...validFiles);

                // Update the file input with valid files only
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;

                updateFileList();
            });

            function updateFileList() {
                fileList.innerHTML = '';
                if (selectedFiles.length > 0) {
                    uploadSection.classList.add('has-files');
                    submitBtn.disabled = false;

                    selectedFiles.forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'file-item';
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        fileItem.innerHTML = `
                            <div style="flex: 1;">
                                <span class="file-name">${file.name}</span>
                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">${fileSizeMB} MB</div>
                            </div>
                            <button type="button" class="file-remove" onclick="removeFile(${index})">Remove</button>
                        `;
                        fileList.appendChild(fileItem);
                    });
                } else {
                    uploadSection.classList.remove('has-files');
                    submitBtn.disabled = true;
                }
            }

            window.removeFile = function(index) {
                selectedFiles.splice(index, 1);
                updateFileList();

                // Update the file input
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            };
        }

        // History Modal Functions
        function openHistoryModal() {
            document.getElementById('historyModal').style.display = 'block';
            // Scroll to bottom of modal body
            const modalBody = document.querySelector('.modal-body');
            if (modalBody) {
                setTimeout(() => {
                    modalBody.scrollTop = modalBody.scrollHeight;
                }, 100);
            }
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('historyModal');
            if (event.target == modal) {
                closeHistoryModal();
            }
        }

        // Auto-scroll to bottom when new comment is added in modal
        document.addEventListener('DOMContentLoaded', function() {
            const modalForms = document.querySelectorAll('#historyModal form');
            modalForms.forEach(form => {
                form.addEventListener('submit', function() {
                    // After form submission, the page will reload and show new comment
                    // This is handled by the server redirect
                });
            });
        });

        // Delete submission file
        function deleteFile(fileId, fileName) {
            if (!confirm(`Are you sure you want to delete "${fileName}"? This action cannot be undone.`)) {
                return;
            }

            const submissionId = {{ $submission->id ?? 'null' }};
            if (!submissionId) return;

            fetch(`/submission/${submissionId}/file/${fileId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove file item from DOM
                    const fileItem = document.getElementById(`file-item-${fileId}`);
                    if (fileItem) {
                        fileItem.style.transition = 'opacity 0.3s';
                        fileItem.style.opacity = '0';
                        setTimeout(() => {
                            fileItem.remove();

                            // Check if no files remain
                            const remainingFiles = document.querySelectorAll('.submitted-file-item');
                            if (remainingFiles.length === 0) {
                                // Reload page to show upload form
                                location.reload();
                            }
                        }, 300);
                    }
                    showSuccessMessage(data.message || 'File deleted successfully.');
                } else {
                    showErrorMessage(data.message || 'Failed to delete file.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('An error occurred while deleting the file.');
            });
        }

        // Edit/Replace submission file
        window.editFile = function(fileId, fileName) {
            // Create file input
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.doc,.docx,.txt,.rtf,.odt';
            input.style.display = 'none';

            // Add change event listener
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file size (20MB)
                const maxSize = 20 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`File size exceeds 20MB limit. Please select a smaller file.`);
                    return;
                }

                // Validate file type
                const allowedTypes = ['application/pdf', 'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain', 'application/rtf', 'application/vnd.oasis.opendocument.text'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please select a PDF, DOC, DOCX, TXT, RTF, or ODT file.');
                    return;
                }

                // Show loading state
                const fileItem = document.getElementById(`file-item-${fileId}`);
                const originalContent = fileItem.innerHTML;
                fileItem.innerHTML = '<span>⏳ Uploading...</span>';

                // Upload file
                const submissionId = {{ $submission->id ?? 'null' }};
                if (!submissionId) return;

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                fetch(`/submission/${submissionId}/file/${fileId}/replace`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message || 'File replaced successfully.');
                        // Reload page to show updated file
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        fileItem.innerHTML = originalContent;
                        showErrorMessage(data.message || 'Failed to replace file.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    fileItem.innerHTML = originalContent;
                    showErrorMessage('An error occurred while replacing the file.');
                });
            });

            // Trigger file input
            document.body.appendChild(input);
            input.click();
            document.body.removeChild(input);
        }

        // Helper function to format dates in Malaysia timezone
        function formatDateMalaysia(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-MY', {
                timeZone: 'Asia/Kuala_Lumpur',
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        // AJAX handler for comment form
        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.getElementById('commentForm');

            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(commentForm);
                    const commentInput = document.getElementById('commentInput');
                    const submitButton = commentForm.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    // Disable button during submission
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        '<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3v1a5 5 0 0 0 5 5h1a1 1 0 1 1 0 2h-1a7 7 0 0 1-7-7V3a1 1 0 1 1 2 0zM3.293 7.293a1 1 0 0 1 1.414 0L6 5.414l1.293 1.879a1 1 0 0 1-1.414 1.414L5 7.243l-1.879 1.464a1 1 0 0 1-1.414-1.414z"/></svg>';

                    fetch(commentForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Clear the input
                                commentInput.value = '';

                                // Create new comment element
                                const commentItem = document.createElement('div');
                                commentItem.className = 'comment-item';

                                const currentUserId = parseInt(commentForm.getAttribute(
                                    'data-current-user-id'));
                                const currentUserName = commentForm.getAttribute(
                                    'data-current-user-name');
                                const userLabel = data.comment.user_role === 'lecturer' ?
                                    'Lecturer' :
                                    (data.comment.user_id === currentUserId ? 'You' : 'Student');

                                const formattedDate = formatDateMalaysia(data.comment.created_at);

                                commentItem.innerHTML = `
                                <div class="comment-author">
                                    ${userLabel}: ${data.comment.user_name}
                                    <span class="comment-role-badge">${data.comment.user_role.charAt(0).toUpperCase() + data.comment.user_role.slice(1)}</span>
                                </div>
                                <div class="comment-text">${data.comment.comment}</div>
                                <div class="comment-time">${formattedDate}</div>
                            `;

                                // Insert the new comment at the top of the comments section
                                const commentsSection = document.querySelector('.comment-list');
                                if (commentsSection) {
                                    // Remove "No comments yet" message if it exists
                                    const noCommentsMsg = commentsSection.querySelector(
                                        '.comment-item .comment-text[style*="color: var(--muted)"]');
                                    if (noCommentsMsg && noCommentsMsg.textContent.includes(
                                            'No comments yet')) {
                                        noCommentsMsg.closest('.comment-item').remove();
                                    }
                                    commentsSection.insertBefore(commentItem, commentsSection
                                        .firstChild);
                                }

                                // Show success message
                                showSuccessMessage(data.message || 'Comment added successfully!');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            const errorMessage = error.message || error.errors?.comment?.[0] ||
                                'An error occurred while submitting your comment.';
                            alert(errorMessage);
                        })
                        .finally(() => {
                            // Re-enable button
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        });
                });
            }

            // AJAX handler for grade form
            const gradeForm = document.getElementById('gradeForm');
            @if(isset($submission))
            const submissionId = {{ $submission->id }};
            @else
            const submissionId = null;
            @endif

            if (gradeForm) {
                gradeForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(gradeForm);
                    const submitButton = gradeForm.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.textContent;

                    // Disable button during submission
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';

                    fetch(gradeForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Update grade display
                                const gradeDisplay = document.getElementById('gradeDisplay');
                                const gradeScore = document.getElementById('gradeScore');
                                const gradeLetter = document.getElementById('gradeLetter');
                                const markedAt = document.getElementById('markedAt');
                                const feedbackDisplay = document.getElementById('feedbackDisplay');
                                const feedbackText = document.getElementById('feedbackText');

                                if (data.submission.score !== null) {
                                    if (gradeScore) gradeScore.textContent = data.submission.score;
                                    if (gradeLetter) gradeLetter.textContent = data.submission.grade ||
                                        '';

                                    if (data.submission.marked_at) {
                                        const formattedDate = formatDateMalaysia(data.submission.marked_at);
                                        if (markedAt) {
                                            markedAt.innerHTML =
                                                `<small style="color: var(--muted);">Marked on: ${formattedDate}</small>`;
                                        }
                                    }

                                    if (gradeDisplay) gradeDisplay.style.display = 'block';
                                }

                                // Update feedback display
                                if (data.submission.lecturer_feedback) {
                                    if (feedbackText) feedbackText.textContent = data.submission
                                        .lecturer_feedback;
                                    if (feedbackDisplay) feedbackDisplay.style.display = 'block';
                                } else {
                                    if (feedbackText) {
                                        feedbackText.innerHTML = '<span style="color: #999; font-style: italic;">No text feedback provided.</span>';
                                    }
                                }

                                // Update feedback files display
                                const feedbackFilesDisplay = document.getElementById('feedbackFilesDisplay');
                                const feedbackFilesList = document.getElementById('feedbackFilesList');
                                
                                if (data.submission.feedback_files && data.submission.feedback_files.length > 0) {
                                    if (!feedbackFilesDisplay) {
                                        // Create feedback files display section if it doesn't exist
                                        const feedbackTextDiv = document.getElementById('feedbackText');
                                        if (feedbackTextDiv && feedbackDisplay) {
                                            const filesDiv = document.createElement('div');
                                            filesDiv.id = 'feedbackFilesDisplay';
                                            filesDiv.style.cssText = 'margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;';
                                            filesDiv.innerHTML = '<strong style="display: block; margin-bottom: 10px; color: #222; font-size: 15px;">📁 Feedback Files:</strong><div id="feedbackFilesList"></div>';
                                            feedbackDisplay.appendChild(filesDiv);
                                        }
                                    }
                                    
                                    const filesList = document.getElementById('feedbackFilesList');
                                    if (filesList && submissionId) {
                                        filesList.innerHTML = data.submission.feedback_files.map(file => {
                                            const fileSizeKB = (file.file_size / 1024).toFixed(2);
                                            return `<div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f5f5f5; border-radius: 4px; margin-bottom: 8px; border: 1px solid #e0e0e0;">
                                                <div style="display: flex; align-items: center; flex: 1; gap: 10px;">
                                                    <span style="font-size: 20px;">📎</span>
                                                    <div style="flex: 1;">
                                                        <a href="/submission/${submissionId}/feedback/${file.id}/download" 
                                                           target="_blank"
                                                           style="color: var(--color-primary); text-decoration: none; font-weight: 500; display: block; margin-bottom: 2px;">
                                                            ${file.original_filename}
                                                        </a>
                                                        <small style="color: var(--muted); font-size: 12px;">
                                                            ${fileSizeKB} KB
                                                        </small>
                                                    </div>
                                                </div>
                                                <a href="/submission/${submissionId}/feedback/${file.id}/download" 
                                                   target="_blank"
                                                   style="padding: 6px 12px; background: var(--color-primary); color: white; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 500; white-space: nowrap;">
                                                    View/Download
                                                </a>
                                            </div>`;
                                        }).join('');
                                    }
                                    
                                    if (feedbackDisplay) feedbackDisplay.style.display = 'block';
                                } else {
                                    if (feedbackFilesDisplay) {
                                        feedbackFilesDisplay.remove();
                                    }
                                    if (!data.submission.lecturer_feedback && feedbackDisplay) {
                                        feedbackDisplay.style.display = 'none';
                                    }
                                }

                                // Reload page to show newly uploaded files (for lecturer view)
                                if (window.location.href.includes('student_id')) {
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }

                                // Show success message
                                showSuccessMessage(data.message ||
                                    'Grade and feedback updated successfully!');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            const errorMessage = error.message || error.errors?.score?.[0] || error
                                .errors?.lecturer_feedback?.[0] ||
                                'An error occurred while saving the grade.';
                            alert(errorMessage);
                        })
                        .finally(() => {
                            // Re-enable button
                            submitButton.disabled = false;
                            submitButton.textContent = originalButtonText;
                        });
                });
            }

            // Feedback file upload handling (same as assignment upload)
            const feedbackFileInput = document.getElementById('feedbackFilesInput');
            const feedbackFileList = document.getElementById('feedbackFileList');
            const feedbackUploadSection = document.getElementById('feedbackUploadSection');
            const selectedFeedbackFiles = [];

            if (feedbackFileInput) {
                feedbackFileInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    const maxSize = 20 * 1024 * 1024; // 20MB in bytes
                    const invalidFiles = [];
                    const validFiles = [];

                    // Validate file sizes
                    files.forEach(file => {
                        if (file.size > maxSize) {
                            invalidFiles.push(file.name);
                        } else {
                            validFiles.push(file);
                        }
                    });

                    // Show error for files that are too large
                    if (invalidFiles.length > 0) {
                        alert(`The following files exceed the 20MB limit:\n${invalidFiles.join('\n')}\n\nPlease select smaller files.`);
                    }

                    // Update selectedFeedbackFiles with valid files
                    selectedFeedbackFiles.length = 0;
                    selectedFeedbackFiles.push(...validFiles);

                    // Update the file input with valid files only
                    const dt = new DataTransfer();
                    selectedFeedbackFiles.forEach(file => dt.items.add(file));
                    feedbackFileInput.files = dt.files;

                    updateFeedbackFileList();
                });

                function updateFeedbackFileList() {
                    feedbackFileList.innerHTML = '';
                    if (selectedFeedbackFiles.length > 0) {
                        feedbackUploadSection.classList.add('has-files');

                        selectedFeedbackFiles.forEach((file, index) => {
                            const fileItem = document.createElement('div');
                            fileItem.className = 'file-item';
                            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                            fileItem.innerHTML = `
                                <div style="flex: 1;">
                                    <span class="file-name">${file.name}</span>
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">${fileSizeMB} MB</div>
                                </div>
                                <button type="button" class="file-remove" onclick="removeFeedbackFile(${index})">Remove</button>
                            `;
                            feedbackFileList.appendChild(fileItem);
                        });
                    } else {
                        feedbackUploadSection.classList.remove('has-files');
                    }
                }

                window.removeFeedbackFile = function(index) {
                    selectedFeedbackFiles.splice(index, 1);
                    updateFeedbackFileList();

                    // Update the file input
                    const dt = new DataTransfer();
                    selectedFeedbackFiles.forEach(file => dt.items.add(file));
                    feedbackFileInput.files = dt.files;
                };
            }

            // Handle feedback file deletion
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('delete-feedback-file')) {
                    e.preventDefault();
                    
                    const button = e.target;
                    const fileId = button.getAttribute('data-file-id');
                    const submissionId = button.getAttribute('data-submission-id');
                    
                    if (!confirm('Are you sure you want to delete this feedback file?')) {
                        return;
                    }
                    
                    button.disabled = true;
                    button.textContent = 'Deleting...';
                    
                    fetch(`/submission/${submissionId}/feedback/${fileId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the file element from the DOM
                            const fileElement = button.closest('div');
                            if (fileElement) {
                                fileElement.remove();
                            }
                            
                            // If no files remain, hide the files list container
                            const filesList = document.getElementById('feedbackFilesList');
                            if (filesList && filesList.children.length === 0) {
                                const filesDisplay = document.getElementById('feedbackFilesDisplay');
                                if (filesDisplay) {
                                    filesDisplay.remove();
                                }
                            }
                            
                            showSuccessMessage(data.message || 'Feedback file deleted successfully.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorMessage = error.message || 'An error occurred while deleting the file.';
                        alert(errorMessage);
                        button.disabled = false;
                        button.textContent = 'Delete';
                    });
                }
            });

            // Helper function to show success messages
            function showSuccessMessage(message) {
                // Check if success alert already exists
                let successAlert = document.getElementById('successAlert');

                if (!successAlert) {
                    // Create success alert element
                    successAlert = document.createElement('div');
                    successAlert.id = 'successAlert';
                    successAlert.className = 'success-alert';
                    successAlert.style.cssText =
                        'position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px 20px; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 10000; max-width: 400px;';
                    document.body.appendChild(successAlert);
                }

                successAlert.textContent = message;
                successAlert.style.display = 'block';

                // Auto-hide after 3 seconds
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>

</html>
