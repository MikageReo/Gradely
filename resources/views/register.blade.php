<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register - GRADELY</title>
    <style>
        /* ...existing or minimal styles... */
        body { font-family: Arial, sans-serif; background:#f4f7f6; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); width:360px; }
        .card h1 { margin:0 0 12px; font-size:20px; }
        .card input { width:100%; padding:10px; margin:8px 0; box-sizing:border-box; }
        .card button { width:100%; padding:10px; background:#00897B; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .muted { font-size:13px; color:#666; text-align:center; margin-top:12px; }
        a { color:#1976D2; text-decoration:none; }
        .role-selection { margin:12px 0; }
        .role-label { display:block; margin:8px 0; }
        .role-label input { width:auto; margin-right:8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Register</h1>

        {{-- show validation errors --}}
        @if ($errors->any())
            <div style="background:#ffeef0;border:1px solid #ffccd5;padding:10px;border-radius:6px;margin-bottom:12px;color:#611a21;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <input type="text" name="name" placeholder="Full name" value="{{ old('name') }}" required />
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            
            <div class="role-selection">
                <label style="font-weight:bold;display:block;margin-bottom:8px;">Select Role:</label>
                <label class="role-label">
                    <input type="radio" name="role" value="student" {{ old('role') == 'student' ? 'checked' : '' }} required />
                    Student
                </label>
                <label class="role-label">
                    <input type="radio" name="role" value="lecturer" {{ old('role') == 'lecturer' ? 'checked' : '' }} required />
                    Lecturer
                </label>
            </div>
            
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="password_confirmation" placeholder="Confirm password" required />
            <button type="submit">Create Account</button>
        </form>

        <div class="muted">
            Already registered? <a href="{{ url('/login') }}">Sign in</a>
        </div>
    </div>
</body>
</html>
