<x-app-layout>
    

    

    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Edit Assessment Type</h1>
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
                        <h5 class="card-title">Edit Assessment Type</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('courseComponents.update', $courseComponents->course_components_id) }}" class="row g-3">
                            @method('patch')
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="courseComponentName" class="form-label">Component Name</label>
                                <input value="{{ $courseComponents->component_name }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="courseComponentName" 
                                    id="inputNanme4"
                                    placeholder="Component Name" required>
                                @if ($errors->has('courseComponentName'))
                                    <span class="text-danger">{{ $errors->first('courseComponentName') }}</span>
                                @endif
                            </div>                          
                            <div class="text-center">
                                <a href="{{ route('courseComponents.index') }}"><button type="button" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">Back</button></a>
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Update Component</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>