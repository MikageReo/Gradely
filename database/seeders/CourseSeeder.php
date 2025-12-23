<?php

namespace Database\Seeders;

use App\Models\Courses;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'course_code' => 'BCS3263',
                'course_name' => 'Software Engineering',
            ],
            [
                'course_code' => 'BCS2234',
                'course_name' => 'Database Systems',
            ],
            [
                'course_code' => 'BCS2143',
                'course_name' => 'Data Structures and Algorithms',
            ],
            [
                'course_code' => 'BCS3456',
                'course_name' => 'Web Development',
            ],
            [
                'course_code' => 'BCS3123',
                'course_name' => 'Object-Oriented Programming',
            ],
            [
                'course_code' => 'BCS4567',
                'course_name' => 'Mobile Application Development',
            ],
            [
                'course_code' => 'BCS2345',
                'course_name' => 'Computer Networks',
            ],
            [
                'course_code' => 'BCS3456',
                'course_name' => 'Artificial Intelligence',
            ],
        ];

        foreach ($courses as $course) {
            Courses::updateOrCreate(
                ['course_code' => $course['course_code']],
                $course
            );
        }

        $this->command->info('Courses seeded successfully!');
    }
}

