<x-guest-layout>
    <body>
        <main>
            <div class="container">
                <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                                <div class="d-flex justify-content-center py-4">
                                    <a href="index.html" class="logo d-flex align-items-center w-auto">
                                        <img src="assets/img/logo.png" alt="">
                                        <span class="d-none d-lg-block">LM-MAX</span>
                                    </a>
                                </div><!-- End Logo -->

                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="pt-4 pb-2">
                                            <h5 class="card-title text-center pb-0 fs-4">Forgot Your Password?</h5>
                                            <p class="text-center small">Enter your email to receive a password reset link</p>
                                        </div>

                                        <div class="mb-4 text-sm text-gray-600 text-center">
                                            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                                        </div>

                                        <!-- Session Status -->
                                        <x-auth-session-status class="mb-4" :status="session('status')" />

                                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('password.email') }}">
                                            @csrf
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email</label>
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                    <input type="email" name="email" class="form-control" id="email" :value="old('email')" required autofocus>
                                                    <div class="invalid-feedback">Please enter your Email.</div>
                                                </div>
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-primary w-100" type="submit">Email Password Reset Link</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main><!-- End #main -->
    </body>
</x-guest-layout>
