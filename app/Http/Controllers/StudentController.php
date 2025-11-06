<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // Show all students
    public function index(Request $request)
{
    $query = Student::with('user');

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('registration_number', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('department', 'like', "%{$search}%");
        });
    }

    // Department filter
    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    // Gender filter
    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    $students = $query->paginate(10)->appends($request->all());

    // Get unique departments for filter dropdown
    $departments = Student::select('department')->distinct()->pluck('department');

    return view('admin.students.index', compact('students', 'departments'));
}

    // Show create form
    public function create()
    {
        return view('admin.students.create');
    }

    // Store new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'department' => 'required|string|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        // Create student
        Student::create([
            'user_id' => $user->id,
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'department' => $validated['department'],
        ]);

        return redirect()->route('students.index')->with('success', 'Student created successfully!');
    }

    // Show student details
    public function show(Student $student)
    {
        $student->load('user', 'courses');
        return view('admin.students.show', compact('student'));
    }

    // Show edit form
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    // Update student
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'department' => 'required|string|max:255',
        ]);

        // Update user
        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update student
        $student->update([
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'department' => $validated['department'],
        ]);

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    // Delete student
    public function destroy(Student $student)
    {
        $student->user->delete(); // This will cascade delete the student
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
