<x-app-layout>
    
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Add User</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item">Forms</li>
            <li class="breadcrumb-item active">Layouts</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add User</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('users.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input value="{{ old('name') }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    id="inputNanme4"
                                    placeholder="Name" required>
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input value="{{ old('email') }}"
                                    type="email" 
                                    class="form-control" 
                                    name="email" 
                                    id="inputEmail4"
                                    placeholder="Email address" required>
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" name="role" required>
                                    <option value="">Select role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('role'))
                                    <span class="text-danger">{{ $errors->first('role') }}</span>
                                @endif
                            </div>                          
                            <div class="text-center">
                                <a href="{{ route('users.index') }}"><button type="button" class="btn btn-secondary">Back</button></a>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>