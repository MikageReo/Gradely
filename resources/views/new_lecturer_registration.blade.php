<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Lecturer Registration - GRADELY</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { margin-bottom: 18px; color: #C62828; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input, select { width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 6px; border: 1px solid #ddd; }
        button { width: 100%; padding: 12px; background: #C62828; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .alert { background: #ffeef0; color: #611a21; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register New Lecturer</h2>
        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.store_user') }}">
            @csrf
            <input type="hidden" name="role" value="lecturer">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <button type="submit">Register Lecturer</button>
        </form>
    </div>
</body>
</html>
