# Database Seeders

This directory contains comprehensive seeders for the GRADELY application.

## Seeder Files

1. **UserSeeder.php** - Creates users (admin, lecturers, students)
2. **CourseSeeder.php** - Creates courses
3. **CourseLecturerSeeder.php** - Assigns lecturers to courses with sections
4. **CourseStudentSeeder.php** - Enrolls students in courses
5. **AssignmentSeeder.php** - Creates assignments for courses
6. **SubmissionSeeder.php** - Creates student submissions with grades and feedback
7. **SubmissionCommentSeeder.php** - Creates comments on submissions

## Usage

### Seed All Data
```bash
php artisan db:seed
```

### Seed Individual Seeders
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=CourseLecturerSeeder
php artisan db:seed --class=CourseStudentSeeder
php artisan db:seed --class=AssignmentSeeder
php artisan db:seed --class=SubmissionSeeder
php artisan db:seed --class=SubmissionCommentSeeder
```

### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

## Default Login Credentials

All users have the password: `password123`

### Admin
- Email: `admin@gradely.com`
- Password: `password123`

### Lecturers
- `ahmad@gradely.com`
- `siti@gradely.com`
- `lim@gradely.com`
- `tan@gradely.com`
- `faiz@gradely.com`

### Students
- `ali@gradely.com`
- `sara@gradely.com`
- `chong@gradely.com`
- `nurul@gradely.com`
- `tan.km@gradely.com`
- `fatimah@gradely.com`
- `lee@gradely.com`
- `hafiz@gradely.com`
- `syafiqah@gradely.com`
- `wong@gradely.com`
- `aminah@gradely.com`
- `lim.yt@gradely.com`
- `izzati@gradely.com`
- `ooi@gradely.com`
- `aisyah@gradely.com`

## Seeded Data Summary

- **1 Admin** user
- **5 Lecturer** users
- **15 Student** users
- **8 Courses** (BCS3263, BCS2234, BCS2143, BCS3456, BCS3123, BCS4567, BCS2345, BCS3456)
- **13 Assignments** across various courses
- **Multiple submissions** (60-80% of enrolled students per assignment)
- **Comments** on 40% of submissions (alternating between student and lecturer)

## Notes

- Seeders use `updateOrCreate` to prevent duplicates
- Submissions include both graded and pending statuses
- Feedback is generated for graded submissions
- Comments include read tracking (lecturer comments are read by students)
- All dates are relative to current time

