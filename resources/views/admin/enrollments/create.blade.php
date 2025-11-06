@extends('layouts.app')

@section('title', 'Enroll Student in Course')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Enroll Student in Course</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Student:</strong> {{ $student->user->name }}<br>
                    <strong>Reg. Number:</strong> {{ $student->registration_number }}
                </div>

                <form method="POST" action="{{ route('enrollments.store', $student) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="course_id" class="form-label">Select Course *</label>
                        <select class="form-select @error('course_id') is-invalid @enderror"
                                id="course_id" name="course_id" required>
                            <option value="">Choose a course...</option>
                            @foreach($availableCourses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->code }} - {{ $course->title }} ({{ $course->credit_units }} units)
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($availableCourses->count() == 0)
                            <small class="text-muted">No available courses to enroll. Student is already enrolled in all courses.</small>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success" {{ $availableCourses->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-check"></i> Enroll Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
