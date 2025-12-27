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
            $table->foreignId('course_lecturer_id')->nullable()->after('lecturer_id')->constrained('course_lecturer')->onDelete('cascade');
            $table->index('course_lecturer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['course_lecturer_id']);
            $table->dropIndex(['course_lecturer_id']);
            $table->dropColumn('course_lecturer_id');
        });
    }
};
