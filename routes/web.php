<?php

use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

// Ensure root renders the welcome page
Route::get('/', function () {
    return view('gradely_welcome_page');
})->name('home');

// Simple pages for login and register views (GET only)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

// Add POST handler for registration form to persist user to database
Route::post('/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:student,lecturer',
    ]);

    $user = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
        'role' => $data['role'],
    ]);

    \Illuminate\Support\Facades\Auth::login($user);

    // Redirect to role-specific dashboard
    if ($user->role === 'student') {
        return redirect('/dashboard/student')->with('success', 'Registered successfully');
    } else {
        return redirect('/dashboard/lecturer')->with('success', 'Registered successfully');
    }
})->name('register.post');

// Protected Dashboard Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Student Dashboard
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    // Lecturer Dashboard
    Route::get('/dashboard/lecturer', function () {
        return response(view('lecturer_dashboard'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('lecturer.dashboard');
});

// Add POST handler for login form
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'role' => 'required|in:student,lecturer',
    ]);

    // Find user with matching email and role
    $user = \App\Models\User::where('email', $data['email'])
        ->where('role', $data['role'])
        ->first();

    // Check if user exists and password matches
    if (!$user || !\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
        return back()
            ->withErrors(['email' => 'Invalid credentials for the selected role.'])
            ->withInput($request->only('email', 'role'));
    }

    \Illuminate\Support\Facades\Auth::login($user);

    // Redirect to role-specific dashboard
    if ($user->role === 'student') {
        return redirect('/dashboard/student')->with('success', 'Login successful');
    } else {
        return redirect('/dashboard/lecturer')->with('success', 'Login successful');
    }
})->name('login.post');

// Logout route
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/')->with('success', 'You have been logged out');
})->name('logout');
