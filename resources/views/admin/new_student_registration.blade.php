@extends('admin.layout')

@section('title', 'Register Student - GRADELY')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Flash Messages -->
    @if (session('success'))
    <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
        {{ session('success') }}
    </div>
    @endif
    
    @if (session('warning'))
    <div style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
        {{ session('warning') }}
    </div>
    @endif
    
    @if (session('error'))
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
        {{ session('error') }}
    </div>
    @endif
    
    @if (session('info'))
    <div style="background: #dbeafe; border: 1px solid #3b82f6; color: #1e40af; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
        {{ session('info') }}
    </div>
    @endif
    
    <!-- Validation Errors -->
    @if ($errors->any())
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: #222; margin-bottom: 8px;">Register New Student</h1>
        <p style="color: var(--muted); font-size: 14px; margin-bottom: 24px;">Add a new student to the system. Login credentials will be sent to their email.</p>
        
        <form method="POST" action="{{ route('admin.store_user') }}">
            @csrf
            <input type="hidden" name="role" value="student">
            
            <!-- Full Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Full Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                       placeholder="Enter student's full name">
            </div>
            
            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                       placeholder="Enter student's email address">
            </div>
            
            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Password *</label>
                <input type="password" id="password" name="password" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                       placeholder="Enter password (min. 8 characters)">
            </div>
            
            <!-- Confirm Password -->
            <div style="margin-bottom: 24px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                       placeholder="Re-enter password">
                <small style="color: var(--muted); margin-top: 4px; display: block;">Login credentials will be sent to the student's email address.</small>
            </div>
            
            <button type="submit" style="width: 100%; padding: 12px; background: var(--color-primary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                Register Student
            </button>
        </form>
    </div>

    <!-- Bulk Registration Section -->
    <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 20px; color: #222; margin-bottom: 4px;">📥 Bulk Register Students (CSV)</h2>
                <p style="color: var(--muted); font-size: 14px;">Upload a CSV file (.csv) to register multiple students at once</p>
            </div>
            <a href="{{ route('admin.download_template') }}" style="padding: 10px 20px; background: var(--color-secondary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 14px; display: inline-block; transition: background 0.2s;">
                📥 Download Template
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.bulk_register') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 20px;">
                <label for="excel" style="display: block; margin-bottom: 6px; font-weight: 500; color: #222;">Upload CSV File (.csv) *</label>
                <input type="file" id="excel" name="excel" accept=".csv" required 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: var(--color-secondary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                Upload & Register Users
            </button>
        </form>
    </div>
@endsection
