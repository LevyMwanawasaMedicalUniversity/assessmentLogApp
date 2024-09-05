<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Add Permission</h1>
        @include('layouts.alerts')
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Permission</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('permissions.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input value="{{ old('name') }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    placeholder="Name" required>
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="text-center">
                                <a href="{{ route('permissions.index') }}"><button type="button" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0y">Back</button></a>
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Save permission</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>