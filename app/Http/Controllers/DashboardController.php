<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Admin dashboard
    public function adminDashboard()
    {
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $recentStudents = Student::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalStudents', 'totalCourses', 'recentStudents'));
    }

    // Student dashboard
    public function studentDashboard()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect('/login')->withErrors(['error' => 'Student profile not found.']);
        }

        $student->load('courses');

        return view('student.dashboard', compact('student'));
    }
}
