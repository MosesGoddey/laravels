<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\GradeHelper;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'department',
        'registration_number',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Relationship: A student belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A student can be enrolled in many courses
     * withPivot() loads additional pivot table columns
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
                    ->withPivot('score', 'grade', 'grade_points', 'status')
                    ->withTimestamps();
    }

    // ========== GPA CALCULATION ==========

    /**
     * Calculate GPA from all completed courses
     *
     * Formula: GPA = (Sum of (Grade Points × Credit Units)) / Total Credit Units
     *
     * Example:
     * Course 1: Grade A (5.00) × 3 units = 15.00
     * Course 2: Grade B (4.00) × 4 units = 16.00
     * Total = 31.00 / 7 units = 4.43 GPA
     */
    public function calculateGPA()
    {
        // Only count completed courses (not enrolled or failed)
        $completedCourses = $this->courses()
            ->wherePivot('status', 'completed')
            ->get();

        // If no completed courses, return 0
        if ($completedCourses->isEmpty()) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        // Loop through each completed course
        foreach ($completedCourses as $course) {
            // Only calculate if grade_points exists
            if ($course->pivot->grade_points !== null) {
                // Add: (grade points × credit units)
                $totalPoints += $course->pivot->grade_points * $course->credit_units;
                // Add: credit units
                $totalCredits += $course->credit_units;
            }
        }

        // Return GPA rounded to 2 decimal places
        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    /**
     * Get academic standing based on GPA
     * Returns: "Excellent", "Very Good", "Good", etc.
     */
    public function getAcademicStanding()
    {
        $gpa = $this->calculateGPA();
        return GradeHelper::getAcademicStanding($gpa);
    }

    /**
     * Get total credit units earned (only from completed courses)
     */
    public function getTotalCreditsEarned()
    {
        return $this->courses()
            ->wherePivot('status', 'completed')
            ->sum('credit_units');
    }

    /**
     * Get number of failed courses
     */
    public function getFailedCoursesCount()
    {
        return $this->courses()
            ->wherePivot('status', 'failed')
            ->count();
    }

    // ========== AUTO-GENERATE REGISTRATION NUMBER ==========

    /**
     * Auto-generate registration number on student creation
     * Format: STD{YEAR}-{SEQUENTIAL}
     * Example: STD2025-001
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->registration_number)) {
                $year = date('Y');
                // Find the last student created this year
                $lastStudent = Student::whereYear('created_at', $year)
                                      ->orderBy('id', 'desc')
                                      ->first();

                // Get next sequential number
                $number = $lastStudent ? intval(substr($lastStudent->registration_number, -3)) + 1 : 1;

                // Format with leading zeros (STD2025-001)
                $student->registration_number = 'STD' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
