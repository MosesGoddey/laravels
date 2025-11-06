@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-chart-line"></i> Analytics Dashboard</h2>
        <p class="text-muted">Comprehensive system statistics and insights</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                <h3>{{ $totalStudents }}</h3>
                <p class="mb-0">Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h3>{{ $totalCourses }}</h3>
                <p class="mb-0">Total Courses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                <h3>{{ $totalEnrollments }}</h3>
                <p class="mb-0">Total Enrollments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h3>{{ $averageGPA }}</h3>
                <p class="mb-0">Average GPA</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <!-- GPA Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> GPA Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="gpaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-building"></i> Students by Department</h5>
            </div>
            <div class="card-body">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <!-- Gender Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-venus-mars"></i> Gender Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Courses -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-trophy"></i> Most Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <canvas id="topCoursesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3 -->
<div class="row mb-4">
    <!-- Enrollment Trends -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Enrollment Trends (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="enrollmentTrendsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row mb-4">
    <!-- Top Performing Students -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-medal"></i> Top Performing Students</h5>
            </div>
            <div class="card-body">
                @if($topStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Name</th>
                                    <th>Reg. Number</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topStudents as $index => $student)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="fas fa-trophy text-warning"></i>
                                            @elseif($index == 1)
                                                <i class="fas fa-medal text-secondary"></i>
                                            @elseif($index == 2)
                                                <i class="fas fa-medal text-danger"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>{{ $student->user->name }}</td>
                                        <td>{{ $student->registration_number }}</td>
                                        <td><strong>{{ $student->calculateGPA() }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted py-3">No graded students yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- At-Risk Students -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> At-Risk Students (GPA < 2.0)</h5>
            </div>
            <div class="card-body">
                @if($atRiskStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reg. Number</th>
                                    <th>GPA</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atRiskStudents as $student)
                                    <tr>
                                        <td>{{ $student->user->name }}</td>
                                        <td>{{ $student->registration_number }}</td>
                                        <td><span class="badge bg-danger">{{ $student->calculateGPA() }}</span></td>
                                        <td>
                                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-success py-3">
                        <i class="fas fa-check-circle"></i> No at-risk students!
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ========== GPA DISTRIBUTION CHART ==========
const gpaCtx = document.getElementById('gpaChart').getContext('2d');
new Chart(gpaCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($gpaDistribution)) !!},
        datasets: [{
            label: 'Number of Students',
            data: {!! json_encode(array_values($gpaDistribution)) !!},
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(0, 123, 255, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(0, 123, 255, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// ========== DEPARTMENT CHART ==========
const deptCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(deptCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($departmentStats->pluck('department')) !!},
        datasets: [{
            data: {!! json_encode($departmentStats->pluck('total')) !!},
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// ========== GENDER CHART ==========
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($genderDistribution->toArray())) !!},
        datasets: [{
            data: {!! json_encode(array_values($genderDistribution->toArray())) !!},
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// ========== TOP COURSES CHART ==========
const topCoursesCtx = document.getElementById('topCoursesChart').getContext('2d');
new Chart(topCoursesCtx, {
    type: 'horizontalBar',
    data: {
        labels: {!! json_encode($topCourses->pluck('code')) !!},
        datasets: [{
            label: 'Number of Students',
            data: {!! json_encode($topCourses->pluck('students_count')) !!},
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// ========== ENROLLMENT TRENDS CHART ==========
const trendsCtx = document.getElementById('enrollmentTrendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($enrollmentTrends->pluck('month')) !!},
        datasets: [{
            label: 'Enrollments',
            data: {!! json_encode($enrollmentTrends->pluck('total')) !!},
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
