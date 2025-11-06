@extends('layouts.app')

@section('title', 'Course Details')

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-book"></i> Course Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Course Code:</th>
                        <td><strong>{{ $course->code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Course Title:</th>
                        <td>{{ $course->title }}</td>
                    </tr>
                    <tr>
                        <th>Credit Units:</th>
                        <td><span class="badge bg-primary">{{ $course->credit_units }} Units</span></td>
                    </tr>
                    <tr>
                        <th>Total Students:</th>
                        <td><span class="badge bg-info">{{ $course->students->count() }}</span></td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $course->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                    <form action="{{ route('courses.destroy', $course) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this course?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> Enrolled Students</h5>
            </div>
            <div class="card-body">
                @if($course->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reg. Number</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Enrolled On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td><strong>{{ $student->registration_number }}</strong></td>
                                        <td>{{ $student->user->name }}</td>
                                        <td>{{ $student->department }}</td>
                                        <td>{{ $student->pivot->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $student) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-graduate fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No students enrolled in this course yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
