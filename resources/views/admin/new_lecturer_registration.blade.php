@extends('admin.layout')

@section('title', 'Register Lecturer - GRADELY')

@section('content')
<div style="max-width: 800px;">
    <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: #222; margin-bottom: 8px;">Register New Lecturer</h1>
        <p style="color: var(--muted); font-size: 14px; margin-bottom: 24px;">Add a new lecturer to the system</p>
        
        <form method="POST" action="{{ route('admin.store_user') }}">
            @csrf
            <input type="hidden" name="role" value="lecturer">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 24px;">
                <label for="email" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 12px; background: var(--color-primary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                Register Lecturer
            </button>
        </form>
    </div>

    <!-- Bulk Registration Section -->
    <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <h2 style="font-size: 20px; color: #222; margin: 0;">📥 Bulk Register Lecturers (CSV)</h2>
            <a href="{{ route('admin.download_template', ['role' => 'lecturer']) }}" 
               target="_blank"
               style="padding: 8px 16px; background: var(--color-secondary); color: var(--white); text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                📥 Download Template
            </a>
        </div>
        <p style="color: var(--muted); font-size: 14px; margin-bottom: 24px;">Upload a CSV file to register multiple lecturers at once</p>
        
        <form method="POST" action="{{ route('admin.bulk_register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" value="lecturer">
            <div style="margin-bottom: 20px;">
                <label for="csv_file" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Upload CSV File *</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <small style="color: var(--muted); margin-top: 4px; display: block;">
                    <strong>CSV Format:</strong> One lecturer per line. Format: <code>Name,Email</code> or just <code>Email</code>
                </small>
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: var(--color-secondary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                Upload & Register Lecturers
            </button>
        </form>
    </div>
@endsection
