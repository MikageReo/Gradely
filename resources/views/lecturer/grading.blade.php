@extends('lecturer.layout')

@section('title', 'Grading - ' . $assignment->title . ' - GRADELY')

@push('styles')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--color-primary);
        text-decoration: none;
        margin-bottom: 20px;
        font-weight: 500;
    }
    .header {
        background: var(--white);
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        margin-bottom: 24px;
    }
    .header h1 {
        font-size: 24px;
        color: #222;
        margin-bottom: 8px;
    }
    .header p {
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
    .submissions-table {
        width: 100%;
        border-collapse: collapse;
    }
    .submissions-table th,
    .submissions-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }
    .submissions-table th {
        font-weight: 600;
        color: var(--muted);
        font-size: 13px;
        text-transform: uppercase;
    }
    .btn-primary {
        background: var(--color-primary);
        color: var(--white);
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #1565C0;
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
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--muted);
    }
</style>
@endpush

@section('content')
<a href="{{ route('lecturer.course.show', $course->id) }}" class="back-link">
    ← Back to Course
</a>

<div class="header">
    <h1>{{ $assignment->title }}</h1>
    <p>{{ $course->course_code }} - {{ $course->course_name }}</p>
</div>

<div class="section">
    <h2 style="margin-bottom: 20px; font-size: 20px; color: #222;">Student Submissions</h2>
    @if($submissions->count() > 0)
        <table class="submissions-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted At</th>
                    <th>Files</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $submission->student->name }}</div>
                            <div style="font-size: 12px; color: var(--muted);">{{ $submission->student->email }}</div>
                        </td>
                        <td>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y g:ia') : 'N/A' }}</td>
                        <td>{{ $submission->submissionFiles->count() }} file(s)</td>
                        <td>
                            @if($submission->score !== null)
                                <span class="badge badge-success">Graded ({{ $submission->score }})</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('assignment.submission', $assignment->id) }}" class="btn-primary">View & Grade</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p>No submissions yet for this assignment.</p>
        </div>
    @endif
</div>
@endsection
