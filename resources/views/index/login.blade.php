<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --error-bg: #ffeef0;
            --error-border: #ffccd5;
            --error-text: #611a21;
            --success-bg: #d4edda;
            --success-border: #c3e6cb;
            --success-text: #155724;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font);
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 50%, #90CAF9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, #1565C0 100%);
            color: var(--white);
            padding: 32px 32px 24px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
        }

        .login-body {
            padding: 32px;
        }

        .success-alert {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error ul {
            margin: 0;
            padding-left: 20px;
        }

        .error li {
            margin: 4px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .role-selection {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
            padding: 16px;
            background: #F5F5F5;
            border-radius: 10px;
        }

        .role-option {
            position: relative;
        }

        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .role-option label {
            display: block;
            padding: 12px 16px;
            background: var(--white);
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            color: var(--muted);
            transition: all 0.2s;
        }

        .role-option input[type="radio"]:checked + label {
            background: var(--color-primary);
            color: var(--white);
            border-color: var(--color-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
        }

        .role-option label:hover {
            border-color: var(--color-primary);
            transform: translateY(-1px);
        }

        .input-field {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
            font-family: var(--font);
            transition: all 0.2s;
            background: var(--white);
        }

        .input-field:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
        }

        .input-field::placeholder {
            color: #999;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--color-primary);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--color-primary) 0%, #1565C0 100%);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(25, 118, 210, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #E0E0E0;
        }

        .login-footer p {
            color: var(--muted);
            font-size: 13px;
        }

        .login-footer a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                border-radius: 12px;
            }

            .login-header {
                padding: 24px 24px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-body {
                padding: 24px;
            }

            .role-selection {
                grid-template-columns: 1fr;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>GRADELY</h1>
            <p>Learning Progress & Performance Portal</p>
        </div>

        <div class="login-body">
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink: 0;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error" id="errorAlert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                @if ($error === 'Invalid credentials for the selected role.')
                                    Wrong credentials. Please check email, password, and role.
                                @else
                                    {{ $error }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Login as:</label>
                    <div class="role-selection">
                        <div class="role-option">
                            <input type="radio" name="role" value="student" id="role_student" {{ old('role') == 'student' ? 'checked' : '' }} required />
                            <label for="role_student">Student</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" value="lecturer" id="role_lecturer" {{ old('role') == 'lecturer' ? 'checked' : '' }} required />
                            <label for="role_lecturer">Lecturer</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" value="admin" id="role_admin" {{ old('role') == 'admin' ? 'checked' : '' }} required />
                            <label for="role_admin">Admin</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="input-field" 
                        placeholder="Enter your email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                    />
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="input-field" 
                            placeholder="Enter your password" 
                            required 
                            autocomplete="current-password"
                        />
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <span id="passwordIcon">👁️</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="login-footer">
                <p>Need help? Contact your administrator</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide alerts
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                successAlert.style.transition = 'opacity 0.3s';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.opacity = '0';
                errorAlert.style.transition = 'opacity 0.3s';
                setTimeout(() => errorAlert.remove(), 5000);
            }, 5000);
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                passwordIcon.textContent = '👁️';
            }
        }

        // Add focus animation to inputs
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
                this.parentElement.style.transition = 'transform 0.2s';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
