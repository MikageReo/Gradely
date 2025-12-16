<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class AdminUserController extends Controller
    {
        public function bulkRegister(Request $request)
        {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            $request->validate([
                'excel' => 'required|file|mimes:xlsx',
            ]);

            $path = $request->file('excel')->getRealPath();
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

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
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $role,
                ]);
                $created++;
            }

            $messages = [];
            if ($created > 0) {
                $messages['success'] = "$created users registered successfully!";
            }
            if (count($duplicateLines) > 0) {
                $firstFive = array_slice($duplicateLines, 0, 5);
                $lines = implode(', ', $firstFive);
                $extra = count($duplicateLines) - 5;
                if ($extra > 0) {
                    $messages['error'] = 'The following lines have emails that already exist: ' . $lines . ' ... and ' . $extra . ' more. You have ' . count($duplicateLines) . ' repeated email fields. Please fix the Excel file.';
                } else {
                    $messages['error'] = 'The following lines have emails that already exist: ' . $lines . '.';
                }
            }
            if (count($errorLines) > 0) {
                if (count($errorLines) <= 3) {
                    $lines = implode(', ', $errorLines);
                    $messages['error'] = ($messages['error'] ?? '') . ' The following lines have invalid or missing data: ' . $lines . '.';
                } else {
                    $messages['error'] = ($messages['error'] ?? '') . ' Some users have invalid data. Please fix the Excel file.';
                }
            }
            return redirect()->route('admin.register_users')->with($messages);
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

            return redirect()->route('admin.dashboard')->with('success', ucfirst($data['role']).' registered successfully!');
        }
    }
