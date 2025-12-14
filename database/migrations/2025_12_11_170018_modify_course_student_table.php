<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists (in case migration partially ran)
        if (!Schema::hasColumn('course_student', 'course_lecturer_id')) {
            Schema::table('course_student', function (Blueprint $table) {
                // First, add the new column without foreign key constraint
                $table->unsignedBigInteger('course_lecturer_id')->nullable()->after('id');
            });
        }

        // Migrate existing course_student data
        // Map course_id to course_lecturer_id (only if course_id column still exists)
        if (Schema::hasColumn('course_student', 'course_id')) {
            $enrollments = DB::table('course_student')->get();
            
            foreach ($enrollments as $enrollment) {
                // Skip if course_lecturer_id is already set
                if ($enrollment->course_lecturer_id) {
                    continue;
                }
                
                // Find the course_lecturer record for this course
                // Get the first course_lecturer for this course (since lecturer_id was already migrated)
                $courseLecturer = DB::table('course_lecturer')
                    ->where('course_id', $enrollment->course_id)
                    ->first();
                
                if ($courseLecturer) {
                    DB::table('course_student')
                        ->where('id', $enrollment->id)
                        ->update(['course_lecturer_id' => $courseLecturer->id]);
                } else {
                    // If no course_lecturer found, we need to create one
                    // This can happen if a course exists but has no course_lecturer record
                    // We'll create a default one with first available lecturer
                    $lecturerId = DB::table('users')->where('role', 'lecturer')->value('id');
                    if (!$lecturerId) {
                        // If no lecturer exists, skip this enrollment or handle error
                        continue;
                    }
                    
                    $courseLecturerId = DB::table('course_lecturer')->insertGetId([
                        'course_id' => $enrollment->course_id,
                        'lecturer_id' => $lecturerId,
                        'section' => null,
                        'capacity' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    DB::table('course_student')
                        ->where('id', $enrollment->id)
                        ->update(['course_lecturer_id' => $courseLecturerId]);
                }
            }
        }

        // Now make course_lecturer_id NOT NULL and add foreign key constraint
        // First, check for NULL or invalid course_lecturer_id values
        $nullRecords = DB::table('course_student')->whereNull('course_lecturer_id')->get();
        foreach ($nullRecords as $record) {
            // Try to find a course_lecturer for any course this student might be enrolled in
            // Since course_id might be gone, we need another approach
            // For now, delete records with NULL course_lecturer_id
            DB::table('course_student')->where('id', $record->id)->delete();
        }
        
        // Check for invalid course_lecturer_id values (that don't exist in course_lecturer table)
        $invalidRecords = DB::table('course_student')
            ->leftJoin('course_lecturer', 'course_student.course_lecturer_id', '=', 'course_lecturer.id')
            ->whereNull('course_lecturer.id')
            ->whereNotNull('course_student.course_lecturer_id')
            ->pluck('course_student.id');
        
        if ($invalidRecords->count() > 0) {
            DB::table('course_student')->whereIn('id', $invalidRecords)->delete();
        }

        // Now make course_lecturer_id NOT NULL
        Schema::table('course_student', function (Blueprint $table) {
            $table->unsignedBigInteger('course_lecturer_id')->nullable(false)->change();
        });

        // Add foreign key constraint separately (check if it doesn't already exist)
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'course_student' 
            AND COLUMN_NAME = 'course_lecturer_id' 
            AND REFERENCED_TABLE_NAME = 'course_lecturer'
        ");
        
        if (empty($foreignKeyExists)) {
            Schema::table('course_student', function (Blueprint $table) {
                $table->foreign('course_lecturer_id')->references('id')->on('course_lecturer')->onDelete('cascade');
            });
        }

        // Finally, drop the old course_id column (if it still exists)
        if (Schema::hasColumn('course_student', 'course_id')) {
            // Check if foreign key exists before dropping
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'course_student' 
                AND COLUMN_NAME = 'course_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                Schema::table('course_student', function (Blueprint $table) {
                    $table->dropForeign(['course_id']);
                });
            }
            
            Schema::table('course_student', function (Blueprint $table) {
                $table->dropColumn('course_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            // Drop course_lecturer_id
            $table->dropForeign(['course_lecturer_id']);
            $table->dropColumn('course_lecturer_id');
            
            // Restore course_id
            $table->foreignId('course_id')->after('id')->constrained('courses')->onDelete('cascade');
        });
    }
};
