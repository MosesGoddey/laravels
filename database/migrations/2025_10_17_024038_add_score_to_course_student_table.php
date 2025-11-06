<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration adds score-based grading to the course_student pivot table
     */
    public function up(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            // score: the percentage score (0-100) entered by admin
            $table->integer('score')->nullable()->after('course_id');

            // grade: automatically calculated letter grade (A, B, C, etc.)
            $table->char('grade', 1)->nullable()->after('score');

            // grade_points: automatically calculated grade points (5.00, 4.00, etc.)
            $table->decimal('grade_points', 3, 2)->nullable()->after('grade');

            // status: track if course is completed, in progress, or failed
            $table->enum('status', ['enrolled', 'completed', 'failed'])->default('enrolled')->after('grade_points');
        });
    }

    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropColumn(['score', 'grade', 'grade_points', 'status']);
        });
    }
};
