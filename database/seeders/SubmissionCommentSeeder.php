<?php

namespace Database\Seeders;

use App\Models\Submissions;
use App\Models\SubmissionComments;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SubmissionCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $submissions = Submissions::all();
        $lecturers = User::where('role', 'lecturer')->get();
        $students = User::where('role', 'student')->get();

        if ($submissions->isEmpty() || $lecturers->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No submissions, lecturers, or students found. Please run previous seeders first.');
            return;
        }

        $comments = [
            // Student questions
            [
                'type' => 'student',
                'text' => 'Hi, I have a question about the requirements. Can you clarify what format you prefer for the submission?',
            ],
            [
                'type' => 'student',
                'text' => 'I\'m having trouble understanding part 3 of the assignment. Could you provide more guidance?',
            ],
            [
                'type' => 'student',
                'text' => 'Is it possible to get an extension for this assignment? I have a valid reason.',
            ],
            [
                'type' => 'student',
                'text' => 'Thank you for the feedback! I will work on improving those areas.',
            ],

            // Lecturer responses
            [
                'type' => 'lecturer',
                'text' => 'Please submit in PDF format. Make sure to include all required sections as outlined in the assignment brief.',
            ],
            [
                'type' => 'lecturer',
                'text' => 'For part 3, you need to analyze the case study and provide recommendations. Refer to chapter 5 of the textbook for guidance.',
            ],
            [
                'type' => 'lecturer',
                'text' => 'I can grant a 2-day extension. Please submit your request through the official channel.',
            ],
            [
                'type' => 'lecturer',
                'text' => 'You\'re welcome! Feel free to reach out if you need further clarification.',
            ],
            [
                'type' => 'lecturer',
                'text' => 'Great improvement! Your revised submission addresses all the feedback points well.',
            ],
            [
                'type' => 'lecturer',
                'text' => 'Consider adding more examples to support your arguments. This will strengthen your submission.',
            ],
        ];

        // Add comments to 40% of submissions
        $submissionsToComment = $submissions->random((int)($submissions->count() * 0.4));

        foreach ($submissionsToComment as $submission) {
            $assignment = $submission->assignment;
            $student = $submission->student;
            
            // Find lecturer assigned to this course
            $courseLecturer = \App\Models\CourseLecturer::where('course_id', $assignment->course_id)
                ->first();
            
            if (!$courseLecturer) {
                continue;
            }

            $lecturer = User::find($courseLecturer->lecturer_id);
            
            if (!$lecturer) {
                continue;
            }

            // Add 1-3 comments per submission
            $commentCount = rand(1, 3);
            
            for ($i = 0; $i < $commentCount; $i++) {
                // Alternate between student and lecturer comments
                $commentType = ($i % 2 == 0) ? 'student' : 'lecturer';
                $user = ($commentType === 'student') ? $student : $lecturer;
                
                $commentData = $comments[array_rand($comments)];
                
                // Make sure we use the right type
                while ($commentData['type'] !== $commentType) {
                    $commentData = $comments[array_rand($comments)];
                }

                $createdAt = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23));
                $readAt = ($commentType === 'lecturer') ? null : $createdAt->copy()->addHours(rand(1, 12)); // Lecturer comments read by students

                SubmissionComments::create([
                    'submission_id' => $submission->id,
                    'user_id' => $user->id,
                    'comment' => $commentData['text'],
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('Submission comments seeded successfully!');
    }
}

