<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRADELY | Learning Portal</title>
    <style>
        /* CSS Variables for easy customization */
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Base Styles */
        body {
            font-family: var(--font);
            background: var(--bg);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
        }
        /* Navigation Header */
        .header {
            width: 100%;
            background: var(--white);
            padding: 12px 5%;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .header-title { font-weight:700; color:var(--color-primary); font-size:18px; }
        .nav-links a { text-decoration:none; color:var(--muted); padding:6px 10px; border-radius:6px; }
        .nav-links .btn-login { background:var(--color-primary); color:var(--white); padding:8px 12px; }
        /* Centered compact card */
        .container { width:100%; display:flex; justify-content:center; padding:40px 16px; }
        .welcome-card {
            width:100%;
            max-width:760px;
            background:var(--white);
            border-radius:10px;
            padding:20px;
            display:flex;
            gap:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.06);
            align-items:center;
        }
        /* Left text, right actions */
        .card-text { flex:1; min-width:200px; }
        .card-text h1 { margin:0 0 6px; font-size:20px; color:#222; }
        .card-text p { margin:0; color:var(--muted); font-size:14px; }
        .card-actions { display:flex; gap:12px; align-items:center; }
        .btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:10px 18px;
            border-radius:8px;
            color:var(--white);
            text-decoration:none;
            font-weight:600;
            font-size:14px;
        }
        .btn-register { background:var(--color-primary); }
        .btn-login  { background:var(--color-secondary); }
        /* Success message styling */
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
        /* Smaller screens: vertical layout */
        @media (max-width:640px){
            .welcome-card { flex-direction:column; text-align:center; }
            .card-actions { width:100%; justify-content:center; }
            .card-text h1 { font-size:18px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-title">GRADELY</div>
        <nav class="nav-links">
            {{-- keep small login on header right for convenience --}}
            <a href="{{ url('/login') }}" class="btn-login">Log In</a>
        </nav>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="success-alert" id="successAlert">
                {{ session('success') }}
            </div>
        @endif
        <section class="welcome-card">
            <div class="card-text">
                <h1>Welcome to GRADELY</h1>
                <p>Learning Progress & Performance portal for BCS3263. Log in to manage courses, view grades and track progress.</p>
            </div>

            <div class="card-actions">
                {{-- Only Login button remains --}}
                <a class="btn btn-login"  href="{{ url('/login') }}">Log In</a>
            </div>
        </section>
    </main>

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