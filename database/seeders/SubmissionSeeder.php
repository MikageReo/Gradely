<?php

namespace Database\Seeders;

use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = Assignments::where('visibility', 'published')->get();
        $students = User::where('role', 'student')->get();

        if ($assignments->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No assignments or students found. Please run previous seeders first.');
            return;
        }

        // Get students enrolled in courses
        $enrolledStudents = [];
        foreach ($students as $student) {
            $courseIds = \App\Models\CourseStudent::where('student_id', $student->id)
                ->join('course_lecturer', 'course_student.course_lecturer_id', '=', 'course_lecturer.id')
                ->pluck('course_lecturer.course_id')
                ->unique();
            
            if ($courseIds->isNotEmpty()) {
                $enrolledStudents[$student->id] = $courseIds->toArray();
            }
        }

        // Create submissions for various assignments
        foreach ($assignments as $assignment) {
            $courseStudents = [];
            
            // Find students enrolled in this assignment's course
            foreach ($enrolledStudents as $studentId => $courseIds) {
                if (in_array($assignment->course_id, $courseIds)) {
                    $courseStudents[] = $studentId;
                }
            }

            // Randomly select 60-80% of enrolled students to submit
            $submissionCount = max(1, (int)(count($courseStudents) * (rand(60, 80) / 100)));
            $selectedStudents = array_slice($courseStudents, 0, $submissionCount);

            foreach ($selectedStudents as $studentId) {
                $submittedAt = Carbon::now()->subDays(rand(0, 10));
                
                // Some submissions are graded, some are pending
                $isGraded = rand(1, 100) <= 70; // 70% chance of being graded
                
                $submission = Submissions::updateOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'file_path' => '', // Legacy field
                        'submitted_at' => $submittedAt,
                        'status' => $isGraded ? 'marked' : 'submitted',
                        'score' => $isGraded ? rand(50, 100) : null,
                        'grade' => $isGraded ? $this->calculateGrade(rand(50, 100)) : null,
                        'lecturer_feedback' => $isGraded ? $this->generateFeedback() : null,
                        'marked_at' => $isGraded ? $submittedAt->addDays(rand(1, 5)) : null,
                    ]
                );
            }
        }

        $this->command->info('Submissions seeded successfully!');
    }

    private function calculateGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    private function generateFeedback()
    {
        $feedbacks = [
            'Good work! Your submission demonstrates a solid understanding of the concepts. Keep up the excellent effort.',
            'Well done! The assignment shows good analytical thinking. Consider expanding on the examples provided.',
            'Excellent submission! Your work is thorough and well-structured. Minor improvements could be made in the conclusion section.',
            'Good attempt. The main points are covered, but some areas need more depth. Please review the course materials.',
            'Satisfactory work. The submission meets the basic requirements. Try to provide more detailed explanations next time.',
            'Well-structured submission with clear explanations. The analysis could be more comprehensive.',
            'Good understanding of the topic demonstrated. Consider adding more real-world examples to strengthen your arguments.',
            'Solid work overall. The submission shows effort, but some technical details need clarification.',
        ];

        return $feedbacks[array_rand($feedbacks)];
    }
}

