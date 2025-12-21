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
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,lecturer',
        ], [
            'name.required' => 'Please add the Full Name field.',
            'email.required' => 'Please add the Email field.',
            'email.email' => 'Please enter a valid email address (e.g. example@gmail.com).',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Please add the Password field.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please select a Role.',
            'role.in' => 'Role must be either Student or Lecturer.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        // Send welcome email with login credentials
        try {
            Mail::to($user->email)->send(new UserRegisteredMail($user, $data['password']));
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            Log::error('Failed to send registration email to ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard')->with('success', ucfirst($data['role']).' registered successfully!');
    }

    public function bulkRegister(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'excel' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        $path = $request->file('excel')->getRealPath();
        $extension = $request->file('excel')->getClientOriginalExtension();
        
        $rows = [];
        
        if (strtolower($extension) === 'csv') {
            // Read CSV file
            if (($handle = fopen($path, 'r')) !== false) {
                // Check for BOM and skip it
                $bom = fread($handle, 3);
                if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                    rewind($handle);
                }
                
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            // Read Excel file using PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        }

        $header = array_map('strtolower', $rows[0]);
        $nameIdx = array_search('name', $header);
        $emailIdx = array_search('email', $header);
        $roleIdx = array_search('role', $header);
        $created = 0;
        $errorLines = [];
        $duplicateLines = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $name = $row[$nameIdx] ?? null;
            $email = $row[$emailIdx] ?? null;
            $role = $row[$roleIdx] ?? null;
            $lineNum = $i + 1; // Excel lines are 1-indexed, header is line 1
            if (!$name || !$email || !in_array($role, ['student', 'lecturer'])) {
                $errorLines[] = $lineNum;
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $duplicateLines[] = $lineNum;
                continue;
            }
            $password = $this->generatePassword();
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
            ]);
            // Send welcome email
            try {
                Mail::to($user->email)->send(new UserRegisteredMail($user, $password));
            } catch (\Exception $e) {
                Log::error('Failed to send registration email to ' . $user->email . ': ' . $e->getMessage());
            }
            $created++;
        }

        $messages = [];
        $totalProcessed = count($rows) - 1; // Exclude header row
        $rejected = count($duplicateLines) + count($errorLines);
        
        // Success message with count
        if ($created > 0) {
            $messages['success'] = "✅ $created user(s) registered successfully!";
        }
        
        // Duplicate emails - show which emails are taken
        if (count($duplicateLines) > 0) {
            // Get the actual duplicate emails for display
            $duplicateEmails = [];
            foreach ($duplicateLines as $lineNum) {
                $row = $rows[$lineNum - 1]; // Convert back to 0-indexed
                $email = $row[$emailIdx] ?? 'Unknown';
                $duplicateEmails[] = $email;
            }
            
            $emailList = implode(', ', array_slice($duplicateEmails, 0, 5));
            $extra = count($duplicateEmails) - 5;
            
            if ($extra > 0) {
                $messages['warning'] = "⚠️ " . count($duplicateLines) . " user(s) rejected - emails already taken: $emailList ... and $extra more.";
            } else {
                $messages['warning'] = "⚠️ " . count($duplicateLines) . " user(s) rejected - emails already taken: $emailList";
            }
        }
        
        // Invalid data errors
        if (count($errorLines) > 0) {
            $lines = implode(', ', array_slice($errorLines, 0, 5));
            $messages['error'] = "❌ " . count($errorLines) . " user(s) rejected - invalid or missing data on line(s): $lines";
        }
        
        // Summary message if there were any issues
        if ($rejected > 0 && $created > 0) {
            $messages['info'] = "📊 Summary: $created added, $rejected rejected out of $totalProcessed total.";
        } elseif ($created == 0) {
            $messages['error'] = ($messages['error'] ?? '') . " No users were registered. Please check your file.";
        }
        
        return back()->with($messages);
    }

    private function generatePassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * Download Excel template for bulk user registration
     * Creates a CSV file that can be opened in Excel
     */
    public function downloadTemplate()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $filename = 'user_registration_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add header row
            fputcsv($file, ['Name', 'Email', 'Role']);
            
            // Add sample data row
            fputcsv($file, ['John Doe', 'john.doe@example.com', 'student']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

