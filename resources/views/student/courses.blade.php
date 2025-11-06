@extends('layouts.app')

@section('title', 'Browse Courses')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-book"></i> Browse Courses</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- My Enrolled Courses -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> My Enrolled Courses</h5>
    </div>
    <div class="card-body">
        @if($enrolledCourses->count() > 0)
            <div class="row">
                @foreach($enrolledCourses as $course)
                    <div class="col-md-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-success">{{ $course->code }}</h6>
                                        <p class="card-text mb-2">{{ $course->title }}</p>
                                        <span class="badge bg-success">{{ $course->credit_units }} Units</span>
                                        <small class="text-muted d-block mt-2">
                                            Enrolled: {{ $course->pivot->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <form action="{{ route('student.courses.unenroll', $course) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to unenroll from this course?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Unenroll">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                Total Credit Units: <strong>{{ $enrolledCourses->sum('credit_units') }}</strong>
            </div>
        @else
            <p class="text-muted text-center py-3">You are not enrolled in any courses yet.</p>
        @endif
    </div>
</div>

<!-- Available Courses -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-book-open"></i> Available Courses</h5>
    </div>
    <div class="card-body">
        @if($availableCourses->count() > 0)
            <div class="row">
                @foreach($availableCourses as $course)
                    <div class="col-md-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title text-primary">{{ $course->code }}</h6>
                                        <p class="card-text mb-2">{{ $course->title }}</p>
                                        <span class="badge bg-primary">{{ $course->credit_units }} Units</span>
                                    </div>
                                    <form action="{{ route('student.courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Enroll">
                                            <i class="fas fa-plus"></i> Enroll
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $availableCourses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>All Caught Up!</h5>
                <p class="text-muted">You are enrolled in all available courses.</p>
            </div>
        @endif
    </div>
</div>
@endsection
