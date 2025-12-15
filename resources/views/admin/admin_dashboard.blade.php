@extends('admin.layout')

@section('title', 'Admin Dashboard - GRADELY')

@section('content')
<div class="header" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-size: 24px; color: #222;">Welcome to Admin Dashboard</h1>
    </div>
    <div style="text-align: right;">
        <p style="color: var(--muted); font-size: 14px;">Logged in as:</p>
        <p style="font-weight: 600; color: #222; font-size: 16px;">{{ Auth::user()->name }}</p>
    </div>
</div>

<div class="cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <div class="card" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <h3 style="color: var(--color-primary); margin-bottom: 10px;">👥 Manage Users</h3>
        <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Add, edit, or remove students and lecturers. <a href="{{ route('admin.create_user') }}" style="color: var(--color-primary); text-decoration: underline;">Register User</a></p>
    </div>
    <div class="card" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <h3 style="color: var(--color-primary); margin-bottom: 10px;">📚 Manage Courses</h3>
        <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Create, update, or delete courses and assign lecturers. <a href="{{ route('admin.courses.index') }}" style="color: var(--color-primary); text-decoration: underline;">Manage Courses</a></p>
    </div>
    <div class="card" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <h3 style="color: var(--color-primary); margin-bottom: 10px;">📝 Manage Assignments</h3>
        <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Oversee all assignments, deadlines, and submissions for all courses.</p>
    </div>
    <div class="card" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <h3 style="color: var(--color-primary); margin-bottom: 10px;">📊 Reports</h3>
        <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">View system usage, student performance, and export data.</p>
    </div>
    <div class="card" style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <h3 style="color: var(--color-primary); margin-bottom: 10px;">⚙️ Settings</h3>
        <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Update system settings, roles, and permissions.</p>
    </div>
</div>
@endsection
