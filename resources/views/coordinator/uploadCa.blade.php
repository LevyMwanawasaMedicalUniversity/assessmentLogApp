<x-app-layout>

    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}"><b>{{ $results->CourseDescription }} - {{$results->CourseName}} : {{$delivery}} {{$assessmentType}}</b></span>
            </h2>
            @include('layouts.alerts')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                    Please ensure that you make your upload under the correct delivery mode (<span style="color:blue"><b>Fulltime</b></span> or <span style="color:green"><b>Distance</b></span>) for each course. Also ensure that your Excel Sheet is correctly formatted and there are no error on the results.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                        <div class="card"><div class="card-header {{ $delivery == 'Fulltime' ? 'bg-primary' : ($delivery == 'Distance' ? 'bg-success' : '') }} text-white">                        
                            <h5><b>Upload {{$assessmentType}} ( {{$delivery}} Education )</b></h5>
                        </div>
                        <div class="card-body">
                            <!-- Vertical Form -->
                            <div class="container px-4">
                                <div class="row pt-3">
                                    <div class="col-12">
                                        @include('coordinator.components.excelSheetorm')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main><!-- End #main -->
</x-app-layout>


