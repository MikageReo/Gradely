<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin users
        User::updateOrCreate(
            ['email' => 'admin@gradely.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Lecturer users
        $lecturers = [
            ['name' => 'Dr. Ahmad bin Abdullah', 'email' => 'ahmad@gradely.com'],
            ['name' => 'Prof. Dr. Siti Nurhaliza', 'email' => 'siti@gradely.com'],
            ['name' => 'Dr. Lim Wei Ming', 'email' => 'lim@gradely.com'],
            ['name' => 'Dr. Tan Mei Ling', 'email' => 'tan@gradely.com'],
            ['name' => 'Dr. Muhammad Faiz', 'email' => 'faiz@gradely.com'],
        ];

        foreach ($lecturers as $lecturer) {
            User::updateOrCreate(
                ['email' => $lecturer['email']],
                [
                    'name' => $lecturer['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'lecturer',
                ]
            );
        }

        // Student users
        $students = [
            ['name' => 'Ali bin Ahmad', 'email' => 'ali@gradely.com'],
            ['name' => 'Sara binti Mohd', 'email' => 'sara@gradely.com'],
            ['name' => 'Chong Wei Jie', 'email' => 'chong@gradely.com'],
            ['name' => 'Nurul Aina', 'email' => 'nurul@gradely.com'],
            ['name' => 'Tan Kian Ming', 'email' => 'tan.km@gradely.com'],
            ['name' => 'Fatimah Zahra', 'email' => 'fatimah@gradely.com'],
            ['name' => 'Lee Jia Wei', 'email' => 'lee@gradely.com'],
            ['name' => 'Muhammad Hafiz', 'email' => 'hafiz@gradely.com'],
            ['name' => 'Nur Syafiqah', 'email' => 'syafiqah@gradely.com'],
            ['name' => 'Wong Chee Keong', 'email' => 'wong@gradely.com'],
            ['name' => 'Aminah binti Hassan', 'email' => 'aminah@gradely.com'],
            ['name' => 'Lim Yee Teng', 'email' => 'lim.yt@gradely.com'],
            ['name' => 'Nur Izzati', 'email' => 'izzati@gradely.com'],
            ['name' => 'Ooi Boon Keat', 'email' => 'ooi@gradely.com'],
            ['name' => 'Siti Aisyah', 'email' => 'aisyah@gradely.com'],
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@gradely.com / password123');
        $this->command->info('Lecturers: ahmad@gradely.com, siti@gradely.com, etc. / password123');
        $this->command->info('Students: ali@gradely.com, sara@gradely.com, etc. / password123');
    }
}

