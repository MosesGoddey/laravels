@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2><i class="fas fa-dashboard"></i> My Dashboard</h2>
        <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> My Profile</h5>
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
                        <td><strong class="text-primary">{{ $student->registration_number }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mt-3 mb-3">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x text-success mb-2"></i>
                <h3 class="mb-0">{{ $student->courses->count() }}</h3>
                <p class="text-muted mb-0">Total Courses</p>
                <hr>
                <h4 class="mb-0 text-primary">{{ $student->courses->sum('credit_units') }}</h4>
                <p class="text-muted mb-0">Total Credit Units</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-book"></i> My Enrolled Courses</h5>
            <a href="{{ route('student.courses.browse') }}" class="btn btn-light btn-sm">
            <i class="fas fa-search"></i> Browse Courses
           </a>
           </div>
            <div class="card-body">
                @if($student->courses->count() > 0)
                    <div class="row">
                        @foreach($student->courses as $course)
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">{{ $course->code }}</h6>
                                        <p class="card-text">{{ $course->title }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">{{ $course->credit_units }} Units</span>
                                            <small class="text-muted">
                                                Enrolled: {{ $course->pivot->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        You are currently enrolled in <strong>{{ $student->courses->count() }}</strong>
                        course(s) with a total of <strong>{{ $student->courses->sum('credit_units') }}</strong> credit units.
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Courses Enrolled</h5>
                        <p class="text-muted">You are not enrolled in any courses yet. Contact your administrator to enroll.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- My Enrolled Courses -->
@foreach($student->courses as $course)
    <div class="col-md-6 mb-3">
        <div class="card {{ $course->pivot->status == 'failed' ? 'border-danger' : 'border-primary' }}">
            <div class="card-body">
                <h6 class="card-title text-primary">{{ $course->code }}</h6>
                <p class="card-text">{{ $course->title }}</p>

                <div class="mb-2">
                    <span class="badge bg-primary">{{ $course->credit_units }} Units</span>

                    @if($course->pivot->score !== null)
                        <span class="badge bg-success">Score: {{ $course->pivot->score }}%</span>
                        <span class="badge bg-info">Grade: {{ $course->pivot->grade }}</span>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($course->pivot->status == 'completed')
                            <span class="badge bg-success"><i class="fas fa-check"></i> Completed</span>
                        @elseif($course->pivot->status == 'failed')
                            <span class="badge bg-danger"><i class="fas fa-times"></i> Failed</span>
                        @else
                            <span class="badge bg-info"><i class="fas fa-clock"></i> In Progress</span>
                        @endif
                    </div>
                    <form action="{{ route('student.courses.unenroll', $course) }}" method="POST"
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- GPA Card -->
<div class="card mt-3">
    <div class="card-body text-center">
        <i class="fas fa-book fa-3x text-success mb-2"></i>
        <h3 class="mb-0">{{ $student->courses->count() }}</h3>
        <p class="text-muted mb-3">Total Courses</p>

        <hr>

        <h4 class="mb-0 text-primary">{{ $student->courses->sum('credit_units') }}</h4>
        <p class="text-muted mb-3">Total Credit Units</p>

        <hr>

        <h4 class="mb-0 text-warning">{{ $student->calculateGPA() }} / 5.00</h4>
        <p class="text-muted mb-2">Current GPA</p>
        <span class="badge bg-info">{{ $student->getAcademicStanding() }}</span>

        <div class="mt-3">
            <small class="text-muted">
                Failed Courses: {{ $student->getFailedCoursesCount() }}<br>
                Credits Earned: {{ $student->getTotalCreditsEarned() }}
            </small>
        </div>
    </div>
</div>
@endsection
