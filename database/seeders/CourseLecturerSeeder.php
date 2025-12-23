<?php

namespace Database\Seeders;

use App\Models\CourseLecturer;
use App\Models\Courses;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseLecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = User::where('role', 'lecturer')->get();
        $courses = Courses::all();

        if ($lecturers->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No lecturers or courses found. Please run UserSeeder and CourseSeeder first.');
            return;
        }

        // Assign lecturers to courses with sections
        $assignments = [
            // BCS3263 - Software Engineering
            ['course_code' => 'BCS3263', 'lecturer_email' => 'ahmad@gradely.com', 'section' => 'A', 'capacity' => 30],
            ['course_code' => 'BCS3263', 'lecturer_email' => 'siti@gradely.com', 'section' => 'B', 'capacity' => 30],
            
            // BCS2234 - Database Systems
            ['course_code' => 'BCS2234', 'lecturer_email' => 'lim@gradely.com', 'section' => 'A', 'capacity' => 25],
            ['course_code' => 'BCS2234', 'lecturer_email' => 'tan@gradely.com', 'section' => 'B', 'capacity' => 25],
            
            // BCS2143 - Data Structures and Algorithms
            ['course_code' => 'BCS2143', 'lecturer_email' => 'faiz@gradely.com', 'section' => 'A', 'capacity' => 35],
            
            // BCS3456 - Web Development
            ['course_code' => 'BCS3456', 'lecturer_email' => 'ahmad@gradely.com', 'section' => 'A', 'capacity' => 30],
            
            // BCS3123 - Object-Oriented Programming
            ['course_code' => 'BCS3123', 'lecturer_email' => 'siti@gradely.com', 'section' => 'A', 'capacity' => 30],
            
            // BCS4567 - Mobile Application Development
            ['course_code' => 'BCS4567', 'lecturer_email' => 'lim@gradely.com', 'section' => 'A', 'capacity' => 25],
            
            // BCS2345 - Computer Networks
            ['course_code' => 'BCS2345', 'lecturer_email' => 'tan@gradely.com', 'section' => 'A', 'capacity' => 30],
        ];

        foreach ($assignments as $assignment) {
            $course = Courses::where('course_code', $assignment['course_code'])->first();
            $lecturer = User::where('email', $assignment['lecturer_email'])->first();

            if ($course && $lecturer) {
                CourseLecturer::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'lecturer_id' => $lecturer->id,
                        'section' => $assignment['section'],
                    ],
                    [
                        'capacity' => $assignment['capacity'],
                    ]
                );
            }
        }

        $this->command->info('Course-Lecturer assignments seeded successfully!');
    }
}

