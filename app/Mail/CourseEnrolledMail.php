<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Courses;
use App\Models\CourseLecturer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseEnrolledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $course;
    public $courseLecturer;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Courses $course, CourseLecturer $courseLecturer)
    {
        $this->user = $user;
        $this->course = $course;
        $this->courseLecturer = $courseLecturer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been enrolled in ' . $this->course->course_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-enrolled',
            with: [
                'user' => $this->user,
                'course' => $this->course,
                'courseLecturer' => $this->courseLecturer,
                'lecturer' => $this->courseLecturer->lecturer,
                'dashboardUrl' => route('student.dashboard'),
            ],
        );
    }
}
