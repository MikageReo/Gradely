<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.admin_create_user');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:student,lecturer',
        ]);

        // Generate random password
        $plainPassword = Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plainPassword),
            'role' => $data['role'],
        ]);

        // Send welcome email
        try {
            Mail::to($user->email)->send(new UserRegisteredMail($user, $plainPassword));
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            Log::error('Failed to send registration email to ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard')->with('success', ucfirst($data['role']).' registered successfully!');
    }

    /**
     * Bulk register users from CSV file
     */
    public function bulkRegister(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'role' => 'required|in:student,lecturer',
        ]);

        $file = $request->file('csv_file');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $registered = 0;
        $skipped = 0;
        $errors = [];

        foreach ($lines as $lineNumber => $line) {
            // Skip header row if present
            if ($lineNumber === 0 && (stripos($line, 'name') !== false || stripos($line, 'email') !== false)) {
                continue;
            }

            // Handle comma-separated values: name,email or just email
            $parts = array_map('trim', explode(',', $line));
            
            $name = '';
            $email = '';

            if (count($parts) >= 2) {
                // Format: name,email
                $name = $parts[0];
                $email = $parts[1];
            } elseif (count($parts) === 1) {
                // Format: email only (generate name from email)
                $email = $parts[0];
                $name = explode('@', $email)[0]; // Use part before @ as name
                $name = ucwords(str_replace(['.', '_', '-'], ' ', $name)); // Format name
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Line " . ($lineNumber + 1) . ": Invalid email format";
                continue;
            }

            // Check if user already exists
            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            // Generate random password
            $password = Str::random(12);
            $plainPassword = $password;

            try {
                $user = User::create([
                    'name' => $name ?: 'User',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $request->role,
                ]);

                // Send welcome email
                try {
                    Mail::to($user->email)->send(new UserRegisteredMail($user, $plainPassword));
                } catch (\Exception $e) {
                    Log::error('Failed to send registration email to ' . $user->email . ': ' . $e->getMessage());
                }

                $registered++;
            } catch (\Exception $e) {
                $errors[] = "Line " . ($lineNumber + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Successfully registered {$registered} " . $request->role . "(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} already exist.";
        }
        if (count($errors) > 0) {
            $errorList = count($errors) > 5 ? implode(', ', array_slice($errors, 0, 5)) . ' and ' . (count($errors) - 5) . ' more' : implode(', ', $errors);
            return back()->with('partial_success', $message)->withErrors(['bulk_errors' => $errorList]);
        }

        return back()->with('success', $message);
    }

    /**
     * Download CSV template for bulk registration
     */
    public function downloadTemplate(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $role = $request->get('role', 'student'); // student or lecturer
        
        $filename = $role === 'lecturer' ? 'lecturer_registration_template.csv' : 'student_registration_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add header row only
            fputcsv($file, ['Name', 'Email']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
