<?php

namespace Database\Seeders;

use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use App\Models\Courses;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courseLecturers = CourseLecturer::all();

        if ($students->isEmpty() || $courseLecturers->isEmpty()) {
            $this->command->warn('No students or course-lecturer assignments found. Please run previous seeders first.');
            return;
        }

        // Enroll students in various courses
        $enrollments = [
            // BCS3263 Section A (Ahmad)
            ['course_code' => 'BCS3263', 'section' => 'A', 'student_emails' => ['ali@gradely.com', 'sara@gradely.com', 'chong@gradely.com', 'nurul@gradely.com', 'tan.km@gradely.com']],
            
            // BCS3263 Section B (Siti)
            ['course_code' => 'BCS3263', 'section' => 'B', 'student_emails' => ['fatimah@gradely.com', 'lee@gradely.com', 'hafiz@gradely.com', 'syafiqah@gradely.com']],
            
            // BCS2234 Section A (Lim)
            ['course_code' => 'BCS2234', 'section' => 'A', 'student_emails' => ['ali@gradely.com', 'sara@gradely.com', 'wong@gradely.com', 'aminah@gradely.com', 'lim.yt@gradely.com']],
            
            // BCS2234 Section B (Tan)
            ['course_code' => 'BCS2234', 'section' => 'B', 'student_emails' => ['izzati@gradely.com', 'ooi@gradely.com', 'aisyah@gradely.com', 'chong@gradely.com']],
            
            // BCS2143 Section A (Faiz)
            ['course_code' => 'BCS2143', 'section' => 'A', 'student_emails' => ['nurul@gradely.com', 'tan.km@gradely.com', 'fatimah@gradely.com', 'lee@gradely.com', 'hafiz@gradely.com']],
            
            // BCS3456 Section A (Ahmad)
            ['course_code' => 'BCS3456', 'section' => 'A', 'student_emails' => ['syafiqah@gradely.com', 'wong@gradely.com', 'aminah@gradely.com', 'lim.yt@gradely.com']],
            
            // BCS3123 Section A (Siti)
            ['course_code' => 'BCS3123', 'section' => 'A', 'student_emails' => ['izzati@gradely.com', 'ooi@gradely.com', 'aisyah@gradely.com', 'ali@gradely.com', 'sara@gradely.com']],
            
            // BCS4567 Section A (Lim)
            ['course_code' => 'BCS4567', 'section' => 'A', 'student_emails' => ['chong@gradely.com', 'nurul@gradely.com', 'tan.km@gradely.com', 'fatimah@gradely.com']],
            
            // BCS2345 Section A (Tan)
            ['course_code' => 'BCS2345', 'section' => 'A', 'student_emails' => ['lee@gradely.com', 'hafiz@gradely.com', 'syafiqah@gradely.com', 'wong@gradely.com']],
        ];

        foreach ($enrollments as $enrollment) {
            $course = Courses::where('course_code', $enrollment['course_code'])->first();
            
            if (!$course) {
                continue;
            }

            $courseLecturer = CourseLecturer::where('course_id', $course->id)
                ->where('section', $enrollment['section'])
                ->first();

            if (!$courseLecturer) {
                continue;
            }

            foreach ($enrollment['student_emails'] as $email) {
                $student = User::where('email', $email)->first();
                
                if ($student) {
                    CourseStudent::updateOrCreate(
                        [
                            'course_lecturer_id' => $courseLecturer->id,
                            'student_id' => $student->id,
                        ]
                    );
                }
            }
        }

        $this->command->info('Student enrollments seeded successfully!');
    }
}

