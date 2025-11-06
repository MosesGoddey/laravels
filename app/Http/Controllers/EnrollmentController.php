<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\GradeHelper;

class EnrollmentController extends Controller
{
    // ========== ADMIN: GRADE ASSIGNMENT ==========

    /**
     * Show form to assign score and grade to a student's course
     */
    public function editGrade(Student $student, Course $course)
    {
        // Get the enrollment record
        $enrollment = $student->courses()->where('course_id', $course->id)->first();

        if (!$enrollment) {
            return redirect()->back()->withErrors(['error' => 'Enrollment not found.']);
        }

        // Get grade scale for display in form
        $gradeScale = GradeHelper::getGradeScale();

        return view('admin.enrollments.grade', compact('student', 'course', 'enrollment', 'gradeScale'));
    }

    /**
     * Save score and automatically calculate grade and grade_points
     */
    public function updateGrade(Request $request, Student $student, Course $course)
    {
        // Validate the input score (0-100)
        $validated = $request->validate([
            'score' => 'required|integer|between:0,100',
            'status' => 'required|in:enrolled,completed,failed',
        ]);

        // Convert score to letter grade using helper
        $grade = GradeHelper::getGradeFromScore($validated['score']);

        // Convert letter grade to grade points using helper
        $gradePoints = GradeHelper::getGradePoints($grade);

        // Update the pivot table with score, grade, and grade_points
        $student->courses()->updateExistingPivot($course->id, [
            'score' => $validated['score'],
            'grade' => $grade,
            'grade_points' => $gradePoints,
            'status' => $validated['status'],
        ]);

        return redirect()->route('students.show', $student)
            ->with('success', "Grade saved! Score: {$validated['score']}, Grade: {$grade}");
    }

    // ========== STUDENT: BROWSE AND ENROLL ==========

    /**
     * Show available courses for student to browse and enroll
     */
    public function browseCourses()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect('/login')->withErrors(['error' => 'Student profile not found.']);
        }

        // Get courses student is already enrolled in
        $enrolledCourseIds = $student->courses->pluck('id')->toArray();

        // Get courses student is NOT enrolled in (available to enroll)
        $availableCourses = Course::whereNotIn('id', $enrolledCourseIds)->paginate(10);

        // Get student's enrolled courses
        $enrolledCourses = $student->courses;

        return view('student.courses', compact('availableCourses', 'enrolledCourses', 'student'));
    }

    /**
     * Student enrolls in a course
     */
    public function selfEnroll(Course $course)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect('/login')->withErrors(['error' => 'Student profile not found.']);
        }

        // Check if already enrolled
        if ($student->courses()->where('course_id', $course->id)->exists()) {
            return back()->withErrors(['error' => 'You are already enrolled in this course.']);
        }

        // Enroll student (status: enrolled by default)
        $student->courses()->attach($course->id);

        return back()->with('success', 'Successfully enrolled in ' . $course->title . '!');
    }

    /**
     * Student removes themselves from a course
     */
    public function selfUnenroll(Course $course)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect('/login')->withErrors(['error' => 'Student profile not found.']);
        }

        // Unenroll student
        $student->courses()->detach($course->id);

        return back()->with('success', 'Successfully unenrolled from ' . $course->title . '.');
    }

    // ========== ADMIN: MANAGE ENROLLMENTS ==========

    /**
     * Show form to enroll a student in a course (admin only)
     */
    public function create(Student $student)
    {
        // Get courses the student is NOT enrolled in
        $enrolledCourseIds = $student->courses->pluck('id')->toArray();
        $availableCourses = Course::whereNotIn('id', $enrolledCourseIds)->get();

        return view('admin.enrollments.create', compact('student', 'availableCourses'));
    }

    /**
     * Admin enrolls a student in a course
     */
    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if already enrolled
        if ($student->courses()->where('course_id', $validated['course_id'])->exists()) {
            return back()->withErrors(['course_id' => 'Student is already enrolled in this course.']);
        }

        // Enroll student
        $student->courses()->attach($validated['course_id']);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student enrolled successfully!');
    }

    /**
     * Admin removes a student from a course
     */
    public function destroy(Student $student, Course $course)
    {
        $student->courses()->detach($course->id);
        return back()->with('success', 'Enrollment removed successfully!');
    }
}
