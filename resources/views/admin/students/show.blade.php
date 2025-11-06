@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Student Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Name:</th>
                        <td>{{ $student->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $student->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $student->phone }}</td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td><span class="badge bg-info">{{ ucfirst($student->gender) }}</span></td>
                    </tr>
                    <tr>
                        <th>Department:</th>
                        <td>{{ $student->department }}</td>
                    </tr>
                    <tr>
                        <th>Reg. Number:</th>
                        <td><strong>{{ $student->registration_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Registered:</th>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Student
                    </a>
                    <form action="{{ route('students.destroy', $student) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this student?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Student
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-book"></i> Enrolled Courses & Grades</h5>
            <a href="{{ route('enrollments.create', $student) }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Enroll in Course
            </a>
        </div>
        <div class="card-body">
            @if($student->courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credits</th>
                                <th>Score</th>
                                <th>Grade</th>
                                <th>Points</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->courses as $course)
                                <tr>
                                    <td><strong>{{ $course->code }}</strong></td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->credit_units }}</td>
                                    <td>
                                        @if($course->pivot->score !== null)
                                            <span class="badge bg-primary">{{ $course->pivot->score }}%</span>
                                        @else
                                            <span class="badge bg-secondary">Not Graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->pivot->grade)
                                            <span class="badge bg-primary">{{ $course->pivot->grade }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->pivot->grade_points !== null)
                                            <strong>{{ number_format($course->pivot->grade_points, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->pivot->status == 'completed')
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Completed</span>
                                        @elseif($course->pivot->status == 'failed')
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Failed</span>
                                        @else
                                            <span class="badge bg-info"><i class="fas fa-clock"></i> Enrolled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('enrollments.grade.edit', [$student, $course]) }}"
                                               class="btn btn-warning" title="Assign Grade">
                                                <i class="fas fa-graduation-cap"></i>
                                            </a>
                                            <form action="{{ route('enrollments.destroy', [$student, $course]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remove this enrollment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="2"><strong>Total Credit Units:</strong></td>
                                <td colspan="6"><strong>{{ $student->courses->sum('credit_units') }}</strong></td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="2"><strong>Current GPA:</strong></td>
                                <td colspan="6">
                                    <strong class="text-primary">{{ $student->calculateGPA() }} / 5.00</strong>
                                    <span class="badge bg-info ms-2">{{ $student->getAcademicStanding() }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No courses enrolled yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
