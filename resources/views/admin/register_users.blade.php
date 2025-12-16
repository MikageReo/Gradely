<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Users - GRADELY</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { margin-bottom: 18px; color: #C62828; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input, select { width: 100%; padding: 10px; margin-bottom: 16px; border-radius: 6px; border: 1px solid #ddd; }
        button { width: 100%; padding: 12px; background: #C62828; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-bottom: 10px; }
        .section { margin-bottom: 32px; }
        .alert { background: #ffeef0; color: #611a21; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
        .or-divider { text-align: center; margin: 24px 0; color: #888; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register Users</h2>
        @if (session('error'))
            <div class="alert alert-danger" id="flash-message-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success" id="flash-message-success">{{ session('success') }}</div>
        @endif
        <script>
            setTimeout(function() {
                var msg1 = document.getElementById('flash-message-error');
                if (msg1) msg1.style.display = 'none';
                var msg2 = document.getElementById('flash-message-success');
                if (msg2) msg2.style.display = 'none';
            }, 5000);
        </script>
        <style>
        .alert-success { background: #d4edda !important; color: #155724 !important; }
        .alert-danger { background: #f8d7da !important; color: #721c24 !important; }
        </style>
        <div class="section">
            <h3>Manual Registration</h3>
            <form method="POST" action="{{ route('admin.register_users.manual') }}">
                @csrf
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password (min 8 characters)</label>
                <input type="password" id="password" name="password" minlength="8" required>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                </select>
                <button type="submit">Register User</button>
            </form>
        </div>
        <div class="or-divider">OR</div>
        <div class="section">
            <h3>Bulk Registration (Excel Upload)</h3>
            <form method="POST" action="{{ route('admin.register_users.bulk') }}" enctype="multipart/form-data">
                @csrf
                <label for="excel">Upload Excel File (.xlsx)</label>
                <input type="file" id="excel" name="excel" accept=".xlsx" required>
                <button type="submit">Upload & Register Users</button>
            </form>
            <small>Excel columns: <b>name</b>, <b>email</b>, <b>role</b> (student or lecturer)</small>
        </div>
    </div>
</body>
</html>
