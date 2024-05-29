<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Permission</h1>
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Permission</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('permissions.update', $permission->id) }}" class="row g-3">
                            @method('patch')
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input value="{{ $permission->name }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    placeholder="Name" required>
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="text-center">
                                <a href="{{ route('permissions.index') }}"><button type="button" class="btn btn-secondary">Back</button></a>
                                <button type="submit" class="btn btn-primary">Save permission</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>