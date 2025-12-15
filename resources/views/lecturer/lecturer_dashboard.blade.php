@extends('lecturer.layout')

@section('title', 'Lecturer Dashboard - GRADELY')

@push('styles')
<style>
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
    /* Course Cards */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }
    .course-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        position: relative;
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
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .course-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.1);
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
    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .course-progress {
        font-size: 13px;
        color: var(--muted);
    }
    /* Pattern backgrounds */
    .pattern-purple {
        background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
    }
    .pattern-blue {
        background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
    }
    .pattern-teal {
        background: linear-gradient(135deg, #00897B 0%, #00695C 100%);
        background-image: 
            radial-gradient(circle at 20px 20px, rgba(255,255,255,0.1) 2px, transparent 0),
            radial-gradient(circle at 60px 60px, rgba(255,255,255,0.1) 2px, transparent 0);
        background-size: 40px 40px;
    }
    .pattern-green {
        background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%);
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
    }
    .pattern-orange {
        background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        background-image: 
            radial-gradient(circle at 20px 20px, rgba(255,255,255,0.1) 2px, transparent 0);
        background-size: 40px 40px;
    }
    .pattern-red {
        background: linear-gradient(135deg, #E53935 0%, #C62828 100%);
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
    }
    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">My courses</h1>
    <p class="page-subtitle">Course overview</p>
</div>

<!-- Course Cards -->
<div class="courses-grid" id="coursesGrid">
    @forelse($courses as $index => $course)
        @php
            $patterns = ['pattern-purple', 'pattern-blue', 'pattern-teal', 'pattern-green', 'pattern-orange', 'pattern-red'];
            $patternClass = $patterns[$index % count($patterns)];
            // Calculate progress (simplified - you can enhance this based on assignments/submissions)
            $totalAssignments = $course->assignments_count ?? 0;
            $progress = $totalAssignments > 0 ? min(100, ($totalAssignments * 10)) : 0;
        @endphp
        <a href="{{ route('lecturer.course.show', $course->id) }}" style="text-decoration: none; color: inherit;">
            <div class="course-card">
                <div class="course-card-header {{ $patternClass }}">
                    <div class="course-code">{{ $course->course_code }}</div>
                </div>
                <div class="course-card-body">
                    <div>
                        <div class="course-title">{{ strtoupper($course->course_name) }}</div>
                        <div class="course-faculty">FACULTY OF COMPUTING</div>
                    </div>
                    <div class="course-footer">
                        <span class="course-progress">{{ $progress }}% complete</span>
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
