<x-app-layout>
    
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Add Assessment Type</h1>
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
                        <h5 class="card-title">Add Assessment Type</h5>

                        <!-- Vertical Form -->
                        <form method="POST" action="{{ route('caAssessmentTypes.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12 mb-3">
                                <label for="assessmentName" class="form-label">Assessment Type</label>
                                <input value="{{ old('assessmentName') }}" 
                                    type="text" 
                                    class="form-control" 
                                    name="assessmentName" 
                                    id="inputNanme4"
                                    placeholder="Assessment Type Name" required>
                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('assessmentName') }}</span>
                                @endif
                            </div>                                                    
                            <div class="text-center">
                                <a href="{{ route('caAssessmentTypes.index') }}"><button type="button" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">Back</button></a>
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

</x-app-layout>