<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,.1);
            border: none;
            border-radius: 10px;
        }
        .btn-primary {
            background: #667eea;
            border: none;
        }
        .btn-primary:hover {
            background: #5568d3;
        }

        /* Password input container styling */
.password-container {
    position: relative;
    margin-bottom: 1rem;
}

.password-input {
    padding-right: 45px !important;
}

.password-toggle-btn {
    position: absolute !important;
    right: 12px !important;
    top: 50% !important;
    transform: translateY(-10%) !important;
    border: none !important;
    background: transparent !important;
    color: #667eea;
    cursor: pointer;
    z-index: 10;
    padding: 0 !important;
    width: 35px !important;
    height: 35px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.3s ease;
}

.password-toggle-btn:hover {
    color: #764ba2;
}

.password-toggle-btn:focus {
    box-shadow: none !important;
    outline: none !important;
}

     /* .navbar-nav .nav-link.active {
       font-weight: bold;
       background-color: rgba(255, 255, 255, 0.1);
       border-radius: 5px;
       } */
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> SMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav me-auto">
    @auth
        @if(Auth::user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="fas fa-dashboard"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/analytics') ? 'active' : '' }}"
           href="{{ route('admin.analytics') }}">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/students*') ? 'active' : '' }}"
           href="{{ route('students.index') }}">
            <i class="fas fa-user-graduate"></i> Students
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/courses*') ? 'active' : '' }}"
           href="{{ route('courses.index') }}">
            <i class="fas fa-book"></i> Courses
        </a>
    </li>
@endif
    @endauth
</ul>
                <ul class="navbar-nav">
                    @auth
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-user"></i> {{ Auth::user()->name }}
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/password-toggle.js') }}"></script>
</body>
</html>
