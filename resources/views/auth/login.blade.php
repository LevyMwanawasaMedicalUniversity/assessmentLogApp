<x-guest-layout>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Left side with image -->
            <div class="col-lg-8 d-none d-lg-block bg-primary position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(65, 84, 241, 0.9) 0%, rgba(46, 202, 106, 0.9) 100%);">
                    <div class="position-absolute top-50 start-50 translate-middle text-white text-center w-75">
                        <h1 class="display-4 fw-bold mb-4">Assessment Log System</h1>
                        <p class="lead mb-4">Streamline your assessment process with our modern, efficient platform</p>
                        <div class="d-flex justify-content-center">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side with login form -->
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
                            <h2 class="card-title fw-bold mb-2">Welcome Back</h2>
                            <p class="text-muted">Enter your credentials to access your account</p>
                        </div>

                        @if(session('status'))
                            <div class="alert alert-success mb-3" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="needs-validation" novalidate method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                           id="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button class="btn btn-primary py-2" type="submit">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
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
