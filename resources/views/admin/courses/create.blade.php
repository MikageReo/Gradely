@extends('admin.layout')

@section('title', 'Create Course - GRADELY')

@push('styles')
<style>
    .content-wrapper { max-width: 800px; margin: 0 auto; }
    .header { background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .form-container { background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
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
@endpush

@section('content')
<div class="content-wrapper">
    <div class="header">
        <h1 style="font-size: 24px; color: #222; margin-bottom: 8px;">Create New Course</h1>
        <p style="color: var(--muted); font-size: 14px;">Add a new course to the system. You can assign lecturers later.</p>
    </div>

    <div class="form-container">
    <form action="{{ route('admin.courses.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label" for="course_code">Course Code *</label>
            <input type="text" class="form-control @error('course_code') border: 1px solid #d32f2f; @enderror" 
                   id="course_code" name="course_code" 
                   value="{{ old('course_code') }}" 
                   placeholder="e.g., CS101" required>
            @error('course_code')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="course_name">Course Name *</label>
            <input type="text" class="form-control @error('course_name') border: 1px solid #d32f2f; @enderror" 
                   id="course_name" name="course_name" 
                   value="{{ old('course_name') }}" 
                   placeholder="e.g., Introduction to Computer Science" required>
            @error('course_name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Course</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
@endsection
