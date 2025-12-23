<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        $this->command->newLine();

        // Seed in order to maintain foreign key relationships
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            CourseLecturerSeeder::class,
            CourseStudentSeeder::class,
            AssignmentSeeder::class,
            SubmissionSeeder::class,
            SubmissionCommentSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Login Credentials:');
        $this->command->info('Admin: admin@gradely.com / password123');
        $this->command->info('Lecturer: ahmad@gradely.com / password123');
        $this->command->info('Student: ali@gradely.com / password123');
    }
}
