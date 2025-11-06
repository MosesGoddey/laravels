<?php

namespace App\Helpers;

class GradeHelper
{
    /**
     * Convert a percentage score to a letter grade and grade points
     *
     * Score Range → Grade → Points
     * 70-100     → A     → 5.00
     * 60-69      → B     → 4.00
     * 50-59      → C     → 3.00
     * 45-49      → D     → 2.00
     * 40-44      → E     → 1.00
     * 0-39       → F     → 0.00
     */
    public static function getGradeFromScore($score)
    {
        // Convert to integer if it's a string
        $score = (int) $score;

        if ($score >= 70) {
            return 'A';
        } elseif ($score >= 60) {
            return 'B';
        } elseif ($score >= 50) {
            return 'C';
        } elseif ($score >= 45) {
            return 'D';
        } elseif ($score >= 40) {
            return 'E';
        } else {
            return 'F';
        }
    }

    /**
     * Get grade points (for GPA calculation) from a letter grade
     */
    public static function getGradePoints($grade)
    {
        $scale = [
            'A' => 5.00,
            'B' => 4.00,
            'C' => 3.00,
            'D' => 2.00,
            'E' => 1.00,
            'F' => 0.00,
        ];

        return $scale[$grade] ?? 0.00;
    }

    /**
     * Get grade scale information (for display in forms)
     */
    public static function getGradeScale()
    {
        return [
            'A' => ['min' => 70, 'max' => 100, 'points' => 5.00],
            'B' => ['min' => 60, 'max' => 69,  'points' => 4.00],
            'C' => ['min' => 50, 'max' => 59,  'points' => 3.00],
            'D' => ['min' => 45, 'max' => 49,  'points' => 2.00],
            'E' => ['min' => 40, 'max' => 44,  'points' => 1.00],
            'F' => ['min' => 0,  'max' => 39,  'points' => 0.00],
        ];
    }

    /**
     * Get academic standing based on GPA
     */
    public static function getAcademicStanding($gpa)
    {
        if ($gpa >= 4.5) {
            return 'Excellent';
        } elseif ($gpa >= 4.0) {
            return 'Very Good';
        } elseif ($gpa >= 3.0) {
            return 'Good';
        } elseif ($gpa >= 2.0) {
            return 'Satisfactory';
        } elseif ($gpa > 0) {
            return 'Pass';
        } else {
            return 'Fail';
        }
    }
}
