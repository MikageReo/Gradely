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
        Schema::create('course_lecturer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('users')->onDelete('cascade');
            $table->string('section')->nullable();
            $table->integer('capacity')->default(0);
            $table->timestamps();
            
            // Ensure unique combination of course, lecturer, and section
            $table->unique(['course_id', 'lecturer_id', 'section'], 'course_lecturer_section_unique');
        });

        // Migrate existing data: Create course_lecturer records from courses table
        // Only if courses table has lecturer_id column
        if (Schema::hasColumn('courses', 'lecturer_id')) {
            $courses = DB::table('courses')->whereNotNull('lecturer_id')->get();
            
            foreach ($courses as $course) {
                // Check if course_lecturer record already exists
                $exists = DB::table('course_lecturer')
                    ->where('course_id', $course->id)
                    ->where('lecturer_id', $course->lecturer_id)
                    ->whereNull('section')
                    ->exists();
                
                if (!$exists) {
                    DB::table('course_lecturer')->insert([
                        'course_id' => $course->id,
                        'lecturer_id' => $course->lecturer_id,
                        'section' => null,
                        'capacity' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lecturer');
    }
};
