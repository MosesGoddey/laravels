@extends('layouts.app')

@section('title', 'Assign Grade')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Assign Grade & Score</h5>
            </div>
            <div class="card-body">
                <!-- Student and Course Info -->
                <div class="alert alert-info mb-4">
                    <strong>Student:</strong> {{ $student->user->name }} ({{ $student->registration_number }})<br>
                    <strong>Course:</strong> {{ $course->code }} - {{ $course->title }}<br>
                    <strong>Credit Units:</strong> {{ $course->credit_units }}
                </div>

                <!-- Grade Scale Reference -->
                <div class="alert alert-secondary mb-4">
                    <h6>Grade Scale Reference:</h6>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Score Range</th>
                                <th>Grade Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gradeScale as $grade => $info)
                                <tr>
                                    <td><strong>{{ $grade }}</strong></td>
                                    <td>{{ $info['min'] }} - {{ $info['max'] }}</td>
                                    <td>{{ number_format($info['points'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Grade Entry Form -->
                <form method="POST" action="{{ route('enrollments.grade.update', [$student, $course]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Score Input (0-100) -->
                    <div class="mb-4">
                        <label for="score" class="form-label"><strong>Enter Score (0-100) *</strong></label>
                        <input type="number"
                               class="form-control @error('score') is-invalid @enderror"
                               id="score"
                               name="score"
                               value="{{ old('score', $enrollment->pivot->score) }}"
                               min="0"
                               max="100"
                               placeholder="Enter score between 0 and 100"
                               required>
                        @error('score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">
                            The grade and grade points will be automatically calculated based on this score.
                        </small>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-4">
                        <label for="status" class="form-label"><strong>Course Status *</strong></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="">Select status...</option>
                            <option value="enrolled" {{ old('status', $enrollment->pivot->status) == 'enrolled' ? 'selected' : '' }}>
                                Enrolled (Still Taking Course)
                            </option>
                            <option value="completed" {{ old('status', $enrollment->pivot->status) == 'completed' ? 'selected' : '' }}>
                                Completed (Final Grade)
                            </option>
                            <option value="failed" {{ old('status', $enrollment->pivot->status) == 'failed' ? 'selected' : '' }}>
                                Failed
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Important Note -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Set status to "Completed" to include this grade in the student's GPA calculation.
                    </div>

                    <!-- Current Grade Display (if exists) -->
                    @if($enrollment->pivot->grade)
                        <div class="alert alert-info">
                            <strong>Current Grade:</strong> {{ $enrollment->pivot->grade }}
                            (Score: {{ $enrollment->pivot->score }}, Points: {{ number_format($enrollment->pivot->grade_points, 2) }})
                        </div>
                    @endif

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Save Grade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
