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
                                        @if (session('message'))
                                            <div class="alert alert-success">{{ session('message') }}</div>
                                        @endif

                                        @if (!session('phone_number_entered'))
                                            <div class="pt-4 pb-2">
                                                <h5 class="card-title text-center pb-0 fs-4">Phone Number</h5>
                                                <p class="text-center small">Enter your mobile number</p>
                                            </div>

                                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('phone.number.store') }}">
                                                @csrf
                                                <div class="col-12">
                                                    <label for="phone_number" class="form-label">Phone Number</label>
                                                    <div class="input-group has-validation">
                                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                        <input type="text" name="phone_number" class="form-control" id="phone_number" required>
                                                        <div class="invalid-feedback">Please enter your Phone Number.</div>
                                                    </div>
                                                    @error('phone_number')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>                                            
                                                <div class="col-12">
                                                    <button class="btn btn-primary w-100" type="submit">Submit</button>
                                                </div>                            
                                            </form>
                                        @else
                                            <div class="pt-4 pb-2">
                                                <h5 class="card-title text-center pb-0 fs-4">Verification Code</h5>
                                                <p class="text-center small">Enter the verification code sent to your phone</p>
                                            </div>

                                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('phone.number.verify') }}">
                                                @csrf
                                                <div class="col-12">
                                                    <label for="two_factor_token" class="form-label">Verification Code</label>
                                                    <div class="input-group has-validation">
                                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                        <input type="text" name="two_factor_token" class="form-control" id="two_factor_token" required>
                                                        <div class="invalid-feedback">Please enter the verification code.</div>
                                                    </div>
                                                    @error('two_factor_token')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>                                            
                                                <div class="col-12">
                                                    <button class="btn btn-primary w-100" type="submit">Verify</button>
                                                </div>                            
                                            </form>
                                        @endif

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
