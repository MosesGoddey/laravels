@extends('layouts.app')

@section('title', 'Login - SMS')

@section('content')
<div class="row">
    <!-- Left side: Typing Animation -->
    <div class="col-md-6">
        <div class="login-hero">
            <div class="hero-content">
                <i class="fas fa-graduation-cap fa-4x text-white mb-4"></i>
                <h1>
                    <span id="typing-text"></span><span id="cursor"></span>
                </h1>
                <p class="hero-subtitle">Your Complete Academic Management Solution</p>
            </div>
        </div>
    </div>

    <!-- Right side: Login Form -->
    <div class="col-md-6">
        <div class="login-form-container">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3>Sign In</h3>
                        <p class="text-muted">Enter your credentials to continue</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 password-container">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control password-input @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>

                        <div class="text-center mt-3">
                            <p class="text-muted">Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none">Create one</a>
                            </p>
                            <p class="text-muted">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot your password?</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Test Credentials -->
            {{-- <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-center mb-2"><i class="fas fa-flask"></i> Test Credentials</h6>
                    <small class="text-muted d-block">
                        <strong>Admin:</strong> admin@sms.com / password
                    </small>
                </div>
            </div> --}}
        </div>
    </div>
</div>

<style>
    .login-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        min-height: 92vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        margin-top: 35px;
        margin-bottom: 70px;
        border-radius: 10px;
    }

    .hero-content {
        text-align: center;
    }

    .login-hero h1 {
        font-size: 42px;
        font-weight: bold;
        margin: 20px 0;
        line-height: 1.4;
        min-height: 60px;
    }

    .hero-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin-top: 20px;
    }

    #cursor {
        display: inline-block;
        width: 3px;
        height: 50px;
        background-color: white;
        margin-left: 8px;
        vertical-align: middle;
        animation: cursor-blink 0.7s infinite;
    }

    @keyframes cursor-blink {
        0%, 49% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }

    .login-form-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 40px 20px;
    }

    @media (max-width: 768px) {
        .login-hero {
            min-height: auto;
            padding: 40px 20px;
        }

        .login-hero h1 {
            font-size: 28px;
        }

        .login-form-container {
            min-height: auto;
            padding: 20px;
        }
    }
</style>

<!-- Inline JavaScript -->
<script>
class TypingDeleteLoop {
    constructor(config = {}) {
        this.elementId = config.elementId;
        this.texts = config.texts || [];
        this.typingSpeed = config.typingSpeed || 100;
        this.deletingSpeed = config.deletingSpeed || 50;
        this.pauseBeforeDelete = config.pauseBeforeDelete || 1500;
        this.pauseBeforeNext = config.pauseBeforeNext || 500;

        this.element = document.getElementById(this.elementId);
        this.currentTextIndex = 0;
        this.currentCharIndex = 0;
        this.isDeleting = false;
    }

    start() {
        this.type();
    }

    type() {
        const currentText = this.texts[this.currentTextIndex];

        if (!this.isDeleting) {
            // Typing phase
            this.currentCharIndex++;
            this.element.textContent = currentText.substring(0, this.currentCharIndex);

            if (this.currentCharIndex === currentText.length) {
                // Finished typing - wait before deleting
                setTimeout(() => {
                    this.isDeleting = true;
                    this.type();
                }, this.pauseBeforeDelete);
                return;
            }

            setTimeout(() => this.type(), this.typingSpeed);
        }
        else {
            // Deleting phase
            this.currentCharIndex--;
            this.element.textContent = currentText.substring(0, this.currentCharIndex);

            if (this.currentCharIndex === 0) {
                // Finished deleting - move to next text
                this.isDeleting = false;
                this.currentTextIndex = (this.currentTextIndex + 1) % this.texts.length;

                setTimeout(() => this.type(), this.pauseBeforeNext);
                return;
            }

            setTimeout(() => this.type(), this.deletingSpeed);
        }
    }
}

// Start animation when page loads
document.addEventListener('DOMContentLoaded', function() {
    const animator = new TypingDeleteLoop({
        elementId: 'typing-text',
        texts: [
            'Welcome to Student Management System',
            'Track Your Academic Journey',
            'Manage Courses with Confidence',
            'Achieve Excellence Together'
        ],
        typingSpeed: 100,
        deletingSpeed: 50,
        pauseBeforeDelete: 1500,
        pauseBeforeNext: 500
    });

    animator.start();
});
</script>
@endsection
