<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        // ========== KEY METRICS ==========
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $totalEnrollments = DB::table('course_student')->count();

        // Calculate overall average GPA
        $students = Student::with('courses')->get();
        $totalGPA = 0;
        $studentsWithGPA = 0;

        foreach ($students as $student) {
            $gpa = $student->calculateGPA();
            if ($gpa > 0) {
                $totalGPA += $gpa;
                $studentsWithGPA++;
            }
        }

        $averageGPA = $studentsWithGPA > 0 ? round($totalGPA / $studentsWithGPA, 2) : 0;

        // ========== GPA DISTRIBUTION ==========
        $gpaDistribution = [
            'Excellent (4.5-5.0)' => 0,
            'Very Good (4.0-4.4)' => 0,
            'Good (3.0-3.9)' => 0,
            'Satisfactory (2.0-2.9)' => 0,
            'Poor (0.1-1.9)' => 0,
            'No Grades' => 0,
        ];

        foreach ($students as $student) {
            $gpa = $student->calculateGPA();

            if ($gpa >= 4.5) {
                $gpaDistribution['Excellent (4.5-5.0)']++;
            } elseif ($gpa >= 4.0) {
                $gpaDistribution['Very Good (4.0-4.4)']++;
            } elseif ($gpa >= 3.0) {
                $gpaDistribution['Good (3.0-3.9)']++;
            } elseif ($gpa >= 2.0) {
                $gpaDistribution['Satisfactory (2.0-2.9)']++;
            } elseif ($gpa > 0) {
                $gpaDistribution['Poor (0.1-1.9)']++;
            } else {
                $gpaDistribution['No Grades']++;
            }
        }

        // ========== DEPARTMENT STATISTICS ==========
        $departmentStats = Student::select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->orderBy('total', 'desc')
            ->get();

        // ========== GENDER DISTRIBUTION ==========
        $genderDistribution = Student::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                return [ucfirst($item->gender) => $item->total];
            });

        // ========== TOP COURSES (Most Enrolled) ==========
        $topCourses = Course::withCount('students')
            ->orderBy('students_count', 'desc')
            ->take(5)
            ->get();

        // ========== AT-RISK STUDENTS ==========
        $atRiskStudents = $students->filter(function ($student) {
            $gpa = $student->calculateGPA();
            return $gpa > 0 && $gpa < 2.0;
        })->take(10);

        // ========== TOP PERFORMING STUDENTS ==========
        $topStudents = $students->filter(function ($student) {
            return $student->calculateGPA() > 0;
        })->sortByDesc(function ($student) {
            return $student->calculateGPA();
        })->take(10);

        // ========== ENROLLMENT TRENDS (Last 6 Months) ==========
        $enrollmentTrends = DB::table('course_student')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ========== COURSE DIFFICULTY (Based on Average Grade) ==========
        $courseDifficulty = Course::withCount('students')
            ->with(['students' => function ($query) {
                $query->wherePivot('status', 'completed');
            }])
            ->get()
            ->map(function ($course) {
                $completedStudents = $course->students->where('pivot.status', 'completed');
                $avgGradePoints = $completedStudents->avg('pivot.grade_points');

                return [
                    'course' => $course->code,
                    'avg_grade' => $avgGradePoints ? round($avgGradePoints, 2) : 0,
                    'total_students' => $course->students_count
                ];
            })
            ->filter(function ($item) {
                return $item['avg_grade'] > 0;
            })
            ->sortBy('avg_grade')
            ->take(5);

        return view('admin.analytics.index', compact(
            'totalStudents',
            'totalCourses',
            'totalEnrollments',
            'averageGPA',
            'gpaDistribution',
            'departmentStats',
            'genderDistribution',
            'topCourses',
            'atRiskStudents',
            'topStudents',
            'enrollmentTrends',
            'courseDifficulty'
        ));
    }
}
