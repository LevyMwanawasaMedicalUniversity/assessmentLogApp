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
                        <form method="POST" action="{{ route('caAssessmentTypes.update', $assessmentTypes->id) }}" class="row g-3">
                            @method('patch')
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="assessmentName" class="form-label">Assessment Name</label>
                                <input value="{{ $assessmentTypes->assesment_type_name }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="assessmentName" 
                                    id="inputNanme4"
                                    placeholder="Name" required>
                                @if ($errors->has('assessmentName'))
                                    <span class="text-danger">{{ $errors->first('assessmentName') }}</span>
                                @endif
                            </div>                          
                            <div class="text-center">
                                <a href="{{ route('caAssessmentTypes.index') }}"><button type="button" class="btn btn-secondary">Back</button></a>
                                <button type="submit" class="btn btn-primary">Update CA Type</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>