<?php

namespace Database\Seeders;

use App\Models\Assignments;
use App\Models\Courses;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Courses::all();
        $lecturers = User::where('role', 'lecturer')->get();

        if ($courses->isEmpty() || $lecturers->isEmpty()) {
            $this->command->warn('No courses or lecturers found. Please run previous seeders first.');
            return;
        }

        $assignments = [
            // BCS3263 - Software Engineering
            [
                'course_code' => 'BCS3263',
                'lecturer_email' => 'ahmad@gradely.com',
                'title' => 'Project Proposal and Requirements Analysis',
                'description' => 'Submit a project proposal with detailed requirements analysis. Include use cases, functional and non-functional requirements.',
                'due_date' => Carbon::now()->addDays(14),
                'status' => 'open',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS3263',
                'lecturer_email' => 'ahmad@gradely.com',
                'title' => 'System Design Document',
                'description' => 'Create a comprehensive system design document including architecture diagrams, database schema, and API specifications.',
                'due_date' => Carbon::now()->addDays(28),
                'status' => 'open',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS3263',
                'lecturer_email' => 'siti@gradely.com',
                'title' => 'Software Testing Report',
                'description' => 'Submit a testing report covering unit tests, integration tests, and test coverage analysis.',
                'due_date' => Carbon::now()->addDays(7),
                'status' => 'open',
                'visibility' => 'published',
            ],

            // BCS2234 - Database Systems
            [
                'course_code' => 'BCS2234',
                'lecturer_email' => 'lim@gradely.com',
                'title' => 'Database Design Assignment',
                'description' => 'Design a normalized database schema for an e-commerce system. Include ER diagrams and normalization process.',
                'due_date' => Carbon::now()->addDays(10),
                'status' => 'open',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS2234',
                'lecturer_email' => 'lim@gradely.com',
                'title' => 'SQL Queries Practice',
                'description' => 'Write complex SQL queries including joins, subqueries, and aggregate functions.',
                'due_date' => Carbon::now()->subDays(5), // Past due
                'status' => 'close',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS2234',
                'lecturer_email' => 'tan@gradely.com',
                'title' => 'Database Optimization Project',
                'description' => 'Analyze and optimize database performance. Include query optimization and indexing strategies.',
                'due_date' => Carbon::now()->addDays(21),
                'status' => 'open',
                'visibility' => 'published',
            ],

            // BCS2143 - Data Structures and Algorithms
            [
                'course_code' => 'BCS2143',
                'lecturer_email' => 'faiz@gradely.com',
                'title' => 'Algorithm Analysis Assignment',
                'description' => 'Analyze time and space complexity of various sorting and searching algorithms.',
                'due_date' => Carbon::now()->addDays(5),
                'status' => 'open',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS2143',
                'lecturer_email' => 'faiz@gradely.com',
                'title' => 'Data Structure Implementation',
                'description' => 'Implement binary search tree, hash table, and graph data structures with full functionality.',
                'due_date' => Carbon::now()->addDays(12),
                'status' => 'open',
                'visibility' => 'published',
            ],

            // BCS3456 - Web Development
            [
                'course_code' => 'BCS3456',
                'lecturer_email' => 'ahmad@gradely.com',
                'title' => 'Responsive Web Design Project',
                'description' => 'Create a responsive website using HTML5, CSS3, and JavaScript. Must work on mobile, tablet, and desktop.',
                'due_date' => Carbon::now()->addDays(15),
                'status' => 'open',
                'visibility' => 'published',
            ],
            [
                'course_code' => 'BCS3456',
                'lecturer_email' => 'ahmad@gradely.com',
                'title' => 'Full-Stack Web Application',
                'description' => 'Develop a complete full-stack web application with frontend and backend integration.',
                'due_date' => Carbon::now()->addDays(30),
                'status' => 'open',
                'visibility' => 'hidden', // Not yet published
            ],

            // BCS3123 - Object-Oriented Programming
            [
                'course_code' => 'BCS3123',
                'lecturer_email' => 'siti@gradely.com',
                'title' => 'OOP Principles Implementation',
                'description' => 'Demonstrate understanding of OOP principles: encapsulation, inheritance, polymorphism, and abstraction.',
                'due_date' => Carbon::now()->addDays(8),
                'status' => 'open',
                'visibility' => 'published',
            ],

            // BCS4567 - Mobile Application Development
            [
                'course_code' => 'BCS4567',
                'lecturer_email' => 'lim@gradely.com',
                'title' => 'Mobile App Prototype',
                'description' => 'Create a mobile app prototype with user interface design and basic functionality.',
                'due_date' => Carbon::now()->addDays(20),
                'status' => 'open',
                'visibility' => 'published',
            ],

            // BCS2345 - Computer Networks
            [
                'course_code' => 'BCS2345',
                'lecturer_email' => 'tan@gradely.com',
                'title' => 'Network Protocol Analysis',
                'description' => 'Analyze TCP/IP protocols and network packet structures. Include Wireshark capture analysis.',
                'due_date' => Carbon::now()->addDays(18),
                'status' => 'open',
                'visibility' => 'published',
            ],
        ];

        foreach ($assignments as $assignmentData) {
            $course = Courses::where('course_code', $assignmentData['course_code'])->first();
            $lecturer = User::where('email', $assignmentData['lecturer_email'])->first();

            if ($course && $lecturer) {
                Assignments::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'lecturer_id' => $lecturer->id,
                        'title' => $assignmentData['title'],
                    ],
                    [
                        'description' => $assignmentData['description'],
                        'due_date' => $assignmentData['due_date'],
                        'status' => $assignmentData['status'],
                        'visibility' => $assignmentData['visibility'],
                    ]
                );
            }
        }

        $this->command->info('Assignments seeded successfully!');
    }
}
