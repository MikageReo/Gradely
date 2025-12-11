<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile - GRADELY</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { margin-bottom: 18px; color: #1976D2; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input { width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 6px; border: 1px solid #ddd; }
        button { width: 100%; padding: 12px; background: #1976D2; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .alert { background: #ffeef0; color: #611a21; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            <button type="submit">Update Profile</button>
        </form>
        <form method="GET" action="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'lecturer' ? route('lecturer.dashboard') : route('student.dashboard')) }}">
            <button type="submit" style="margin-top:10px;background:#aaa;color:#fff;">Back</button>
        </form>
    </div>
</body>
</html>
