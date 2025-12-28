@extends('lecturer.layout')

@section('title', 'Lecturer Dashboard - GRADELY')

@push('styles')
<style>
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
    .page-header {
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #222;
        margin-bottom: 4px;
    }
    .page-subtitle {
        font-size: 16px;
        color: var(--muted);
        font-weight: 400;
    }
    /* Course Cards - Matching Student Dashboard Style */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .course-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        height: 200px;
        display: flex;
        flex-direction: column;
    }
    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .course-card-header {
        height: 120px;
        position: relative;
        background: linear-gradient(135deg, #004D40 0%, #00796B 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .course-code {
        position: relative;
        z-index: 1;
        font-size: 24px;
        font-weight: 700;
        color: var(--white);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .course-card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .course-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 4px;
        line-height: 1.4;
    }
    .course-faculty {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 8px;
    }
    .course-progress {
        font-size: 13px;
        color: var(--muted);
    }
    @media (max-width: 1024px) {
        .courses-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
        }
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="header">
    <div>
        <h1>Welcome to Your Dashboard</h1>
    </div>
    <div class="user-info">
        <p>Logged in as:</p>
        <p class="user-name">{{ Auth::user()->name }}</p>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">My courses</h1>
    <p class="page-subtitle">Course overview</p>
</div>

<!-- Course Cards -->
<div class="courses-grid" id="coursesGrid">
    @forelse($courses as $course)
        <a href="{{ route('lecturer.course.show', $course->id) }}" style="text-decoration: none; color: inherit;">
            <div class="course-card">
                <div class="course-card-header">
                    <div class="course-code">{{ $course->course_code }}</div>
                </div>
                <div class="course-card-body">
                    <div>
                        <div class="course-title">{{ strtoupper($course->course_name) }}</div>
                        <div class="course-faculty">FACULTY OF COMPUTING</div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--muted);">
            <p style="font-size: 18px; margin-bottom: 8px;">No courses found</p>
            <p style="font-size: 14px;">You haven't been assigned to any courses yet.</p>
        </div>
    @endforelse
</div>
@endsection
