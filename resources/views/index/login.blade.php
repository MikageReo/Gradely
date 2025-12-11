<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - GRADELY</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7f6; margin:0; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .card { background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); width:360px; }
        h1 { margin:0 0 16px; font-size:20px; }
        input { width:100%; padding:10px; margin:8px 0; box-sizing:border-box; border:1px solid #e6e6e6; border-radius:6px; }
        button { width:100%; padding:10px; background:#1976D2; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600; margin-top:8px; }
        .muted { text-align:center; margin-top:14px; color:#666; font-size:13px; }
        a { color:#1976D2; text-decoration:none; }
        .role-selection { margin:14px 0; }
        .role-label { display:block; margin:10px 0; }
        .role-label input { width:auto; margin-right:8px; }
        .error { background:#ffeef0; border:1px solid #ffccd5; padding:10px; border-radius:6px; margin-bottom:12px; color:#611a21; font-size:13px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Sign In</h1>
        
        {{-- show validation errors --}}
        @if ($errors->any())
            <div class="error">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="role-selection">
                <label style="font-weight:bold;display:block;margin-bottom:10px;">Login as:</label>
                <label class="role-label">
                    <input type="radio" name="role" value="student" {{ old('role') == 'student' ? 'checked' : '' }} required />
                    Student
                </label>
                <label class="role-label">
                    <input type="radio" name="role" value="lecturer" {{ old('role') == 'lecturer' ? 'checked' : '' }} required />
                    Lecturer
                </label>
                <label class="role-label">
                    <input type="radio" name="role" value="admin" {{ old('role') == 'admin' ? 'checked' : '' }} required />
                    Admin
                </label>
            </div>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Log In</button>
        </form>
        <!-- Register link removed -->
    </div>
</body>
</html>
