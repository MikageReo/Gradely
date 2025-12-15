@extends('admin.layout')

@section('title', 'Manage Courses - GRADELY')

@push('styles')
<style>
    .header { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500; transition: all 0.2s; }
    .btn-primary { background: var(--color-primary); color: var(--white); }
    .btn-primary:hover { background: #B71C1C; }
    .btn-secondary { background: var(--color-secondary); color: var(--white); }
    .btn-secondary:hover { background: #00695C; }
    .btn-danger { background: #d32f2f; color: var(--white); }
    .btn-danger:hover { background: #b71c1c; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
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
@endpush

@section('content')
<div class="header">
    <h1 style="font-size: 24px; color: #222;">Manage Courses</h1>
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
@endsection
