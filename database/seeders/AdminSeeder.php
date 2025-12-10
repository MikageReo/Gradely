<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Create or update the admin user
User::updateOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'name' => 'Admin',
        'password' => Hash::make('12345678'),
        'role' => 'admin',
    ]
);

// Create or update the lecturer user
User::updateOrCreate(
    ['email' => 'lecturer@gmail.com'],
    [
        'name' => 'Lecturer',
        'password' => Hash::make('12345678'),
        'role' => 'lecturer',
    ]
);

// Create or update the student user
User::updateOrCreate(
    ['email' => 'student@gmail.com'],
    [
        'name' => 'Student',
        'password' => Hash::make('12345678'),
        'role' => 'student',
    ]
);

echo "Admin user created or updated.\n";
