<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrollment Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #1976D2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
        .course-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #1976D2;
            margin: 20px 0;
        }
        .course-info-item {
            margin: 10px 0;
            padding: 8px 0;
        }
        .course-info-item strong {
            color: #1976D2;
            display: inline-block;
            min-width: 120px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Course Enrollment Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p>You have been successfully enrolled in a new course. Here are the details:</p>
            
            <div class="course-info">
                <div class="course-info-item">
                    <strong>Course Code:</strong> {{ $course->course_code }}
                </div>
                <div class="course-info-item">
                    <strong>Course Name:</strong> {{ $course->course_name }}
                </div>
                @if($courseLecturer->section)
                    <div class="course-info-item">
                        <strong>Section:</strong> {{ $courseLecturer->section }}
                    </div>
                @endif
                @if($lecturer)
                    <div class="course-info-item">
                        <strong>Lecturer:</strong> {{ $lecturer->name }}
                    </div>
                @endif
            </div>
            
            <p>You can now access course materials, assignments, and announcements through your dashboard.</p>
            
            <div style="text-align: center;">
                <a href="{{ $dashboardUrl }}" class="button">Go to Dashboard</a>
            </div>
            
            <p>If you have any questions, please contact your lecturer or the administration.</p>
            
            <p>Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>

