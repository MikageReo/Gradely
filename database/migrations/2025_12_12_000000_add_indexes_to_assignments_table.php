<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Add indexes for frequently queried columns (Capacity improvement)
            $table->index('course_id');
            $table->index('lecturer_id');
            $table->index('visibility');
            $table->index('status');
            $table->index('due_date');
            // Composite index for common query pattern (course_id + visibility)
            $table->index(['course_id', 'visibility'], 'assignments_course_visibility_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
            $table->dropIndex(['lecturer_id']);
            $table->dropIndex(['visibility']);
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex('assignments_course_visibility_index');
        });
    }
};

