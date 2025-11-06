<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController; 

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        return redirect('/student/dashboard');
    }
    return redirect('/login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========== ADMIN ROUTES ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');


    // Students CRUD
    Route::resource('students', StudentController::class);

    // Courses CRUD
    Route::resource('courses', CourseController::class);

    // Enrollments (Admin enrolling students)
    Route::get('/students/{student}/enroll', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/students/{student}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('/students/{student}/courses/{course}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Grade management routes (Admin assigning grades)
    Route::get('/students/{student}/courses/{course}/grade', [EnrollmentController::class, 'editGrade'])->name('enrollments.grade.edit');
    Route::put('/students/{student}/courses/{course}/grade', [EnrollmentController::class, 'updateGrade'])->name('enrollments.grade.update');
});

// ========== STUDENT ROUTES ==========
Route::middleware(['auth', 'student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('student.dashboard');

    // Self-enrollment routes (Student browsing and enrolling in courses)
    Route::get('/courses', [EnrollmentController::class, 'browseCourses'])->name('student.courses.browse');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'selfEnroll'])->name('student.courses.enroll');
    Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'selfUnenroll'])->name('student.courses.unenroll');
});
