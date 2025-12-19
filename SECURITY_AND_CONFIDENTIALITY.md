# Security and Confidentiality Documentation

## Overview
This document outlines the security measures implemented to protect comments, marks, and feedback in the Gradely system.

## Access Control

### Student Access
- **Comments**: Students can only view and post comments on their own submissions
- **Grades**: Students can only view their own grades and feedback
- **Files**: Students can only download:
  - Assignment attachments for courses they're enrolled in
  - Their own submission files
- **Enrollment Verification**: All student access is verified through `course_student` and `course_lecturer` relationships

### Lecturer Access
- **Comments**: Lecturers can only view and post comments on submissions for assignments in courses they're assigned to
- **Grades**: Lecturers can only view and edit grades for:
  - Assignments in courses they're assigned to (via `course_lecturer`)
  - Assignments they created
- **Files**: Lecturers can download:
  - Assignment attachments for courses they're assigned to
  - Submission files for students enrolled in their courses
- **Student Selection**: Lecturers must specify `student_id` parameter when viewing/commenting on submissions to prevent unauthorized access

### Access Control Implementation
All controller methods implement the following checks:
1. **Role Verification**: `role === 'student'` or `role === 'lecturer'`
2. **Course Enrollment**: Verification through `course_student` and `course_lecturer` tables
3. **Assignment Ownership**: Lecturers must be assigned to the course or be the assignment creator
4. **Student-Specific Data**: All queries filter by `student_id` to ensure students only see their own data

## Visibility Boundaries

### Comments Privacy
- **Per-Student Privacy**: Comments are stored per-submission, ensuring private conversations between lecturer and individual student
- **No Cross-Student Visibility**: Students cannot see comments from other students
- **Lecturer Scope**: Lecturers only see comments for submissions they're grading

### Grades Privacy
- **Student-Specific**: Grades are only visible to:
  - The student who submitted the assignment
  - The lecturer assigned to grade it
  - Administrators (if applicable)
- **No Public Display**: Grades are never displayed publicly or to other students

## Data Protection

### File Storage
- **Assignment Attachments**: Stored in `public/assignments/` directory
- **Submission Files**: Stored in `public/submissions/{submission_id}/` directory
- **Protected Downloads**: All file downloads go through protected routes that verify:
  - User authentication
  - Course enrollment (for students)
  - Course assignment (for lecturers)
  - File ownership (for submission files)

### Database Storage
- **Comments**: Stored in `submission_comments` table, linked to specific submissions
- **Grades**: Stored in `submissions` table with `student_id` foreign key
- **No Sensitive Data in Logs**: Comment text and grades are never logged to `laravel.log`

### Logging Practices
- **Email Errors Only**: Only email sending failures are logged (no sensitive content)
- **No Comment/Grade Logging**: Comment text and grade values are never written to logs
- **Error Messages**: Generic error messages that don't expose sensitive data

## Session and Transport Security

### Session Configuration
- **Lifetime**: 120 minutes (2 hours) of inactivity
- **Expire on Close**: Configurable via `SESSION_EXPIRE_ON_CLOSE` environment variable
- **Encryption**: Configurable via `SESSION_ENCRYPT` environment variable
- **Secure Cookies**: Recommended to use secure cookies in production (HTTPS)

### Transport Security
- **HTTPS Recommendation**: Use HTTPS in production to encrypt:
  - Comments in transit
  - Grades in transit
  - Authentication credentials
  - Session cookies

### Session Timeout Best Practices
1. **Automatic Logout**: Sessions expire after 120 minutes of inactivity
2. **Manual Logout**: Users should log out when finished, especially on shared devices
3. **Browser Close**: Consider enabling `SESSION_EXPIRE_ON_CLOSE` for additional security
4. **Shared Devices**: Users should always log out on shared/public computers

## Protected Routes

### File Download Routes
- `/assignment/{assignmentId}/attachment/download` - Protected assignment attachment download
- `/submission/{submissionId}/file/{fileId}/download` - Protected submission file download

Both routes verify:
- User authentication
- Course enrollment/assignment
- File ownership (for submissions)

### Submission Routes
- `/assignment/{assignmentId}/submission` - Requires `student_id` parameter for lecturers
- `/assignment/{assignmentId}/submission/comment` - Requires `student_id` parameter for lecturers
- `/assignment/{assignmentId}/submission/grade` - Only accessible to assigned lecturers

## Implementation Details

### Controller Methods
All methods in `SubmissionController` implement:
1. Role-based access control
2. Course enrollment verification
3. Student-specific data filtering
4. Lecturer assignment verification

### Query Filtering
- All queries filter by `course_id`, `student_id`, and `lecturer_id` to prevent unauthorized access
- URL parameter manipulation is prevented through server-side verification

### View Filtering
- Views only display data the current user is authorized to see
- Comments are filtered by submission (which is already filtered by student)
- Grades are only shown to the submitting student and assigned lecturer

## Recommendations

### For Production Deployment
1. **Enable HTTPS**: Configure SSL/TLS certificates
2. **Session Security**: Set `SESSION_EXPIRE_ON_CLOSE=true` for shared devices
3. **Environment Variables**: Configure secure session settings in `.env`
4. **Regular Audits**: Periodically review access logs for unauthorized access attempts
5. **User Education**: Inform users about:
   - Logging out on shared devices
   - Not sharing login credentials
   - Reporting suspicious activity

### For Development
1. **Test Access Control**: Verify all routes with different user roles
2. **Test URL Manipulation**: Attempt to access other users' data by changing IDs
3. **Verify File Downloads**: Test that protected file routes work correctly
4. **Check Logs**: Ensure no sensitive data is being logged

## Compliance Notes
- **Data Protection**: Comments, marks, and feedback are stored securely in the database
- **Access Logging**: Authentication and authorization checks are performed on every request
- **Privacy**: Each student's data is isolated and only accessible to authorized parties
- **Audit Trail**: All grade updates and comments are timestamped and linked to specific users

