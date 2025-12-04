<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Student Dashboard - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font);
            background: var(--bg);
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--color-primary);
            color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .sidebar a {
            display: block;
            color: var(--white);
            text-decoration: none;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .header {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #222;
        }
        .user-info {
            text-align: right;
        }
        .user-info p {
            color: var(--muted);
            font-size: 14px;
        }
        .user-name {
            font-weight: 600;
            color: #222;
            font-size: 16px;
        }
        .success-alert {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 16px;
            text-align: center;
            z-index: 1000;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
        .success-alert.hide {
            animation: slideUp 0.3s ease-out;
        }
        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .card h3 {
            color: var(--color-primary);
            margin-bottom: 10px;
        }
        .card p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .main-content {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="#courses">📚 Courses</a>
            <a href="#grades">📊 Grades</a>
            <a href="#assignments">✏️ Assignments</a>
            <a href="#progress">📈 Progress</a>
            <a href="#profile">👤 Profile</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="header">
                <div>
                    <h1>Welcome to Your Dashboard</h1>
                </div>
                <div class="user-info">
                    <p>Logged in as:</p>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                </div>
            </div>

            <div class="cards">
                <div class="card" id="courses">
                    <h3>📚 Courses</h3>
                    <p>You are currently enrolled in 3 courses. View your courses, lecture materials, and course schedule here.</p>
                </div>
                <div class="card" id="grades">
                    <h3>📊 Grades</h3>
                    <p>Track your academic performance. View your grades for all assessments, assignments, and exams.</p>
                </div>
                <div class="card" id="assignments">
                    <h3>✏️ Assignments</h3>
                    <p>View pending and submitted assignments. Keep track of deadlines and submission status.</p>
                </div>
                <div class="card" id="progress">
                    <h3>📈 Progress</h3>
                    <p>Monitor your overall academic progress and see personalized recommendations for improvement.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Auto-hide success alert after 4 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.add('hide');
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 4000);
        }
    </script>
</body>
</html>
