<x-guest-layout>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Left side with image -->
            <div class="col-lg-8 d-none d-lg-block bg-primary position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(65, 84, 241, 0.9) 0%, rgba(46, 202, 106, 0.9) 100%);">
                    <div class="position-absolute top-50 start-50 translate-middle text-white text-center w-75">
                        <h1 class="display-4 fw-bold mb-4">Join Assessment Log System</h1>
                        <p class="lead mb-4">Create your account to access our comprehensive assessment management platform</p>
                        <div class="d-flex justify-content-center">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side with registration form -->
            <div class="col-lg-4 col-md-12 d-flex flex-column align-items-center justify-content-center">
                <div class="d-flex justify-content-center py-4 d-lg-none">
                    <a href="{{ route('login') }}" class="logo d-flex align-items-center w-auto">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="me-2">
                        <span class="fs-4 fw-bold text-primary">LM-MAX</span>
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-3 w-100" style="max-width: 450px;">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold mb-2">Create Account</h2>
                            <p class="text-muted">Fill in your details to register</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="needs-validation">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required autofocus autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" required autocomplete="username">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                           required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Password must be at least 8 characters long
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control border-start-0 @error('password_confirmation') is-invalid @enderror" 
                                           required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    <i class="bi bi-person-plus me-2"></i>Register
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-decoration-none text-primary">
                                        Sign in
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 mb-5">
                    <p class="text-muted">
                        &copy; {{ date('Y') }} Levy Mwanawasa Medical University. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
