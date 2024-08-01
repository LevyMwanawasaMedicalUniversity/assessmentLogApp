<x-app-layout>

    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $results->CourseDescription }} - {{$results->CourseName}} <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">{{$delivery}} {{$assessmentType}}</span>
            </h2>
            @include('layouts.alerts')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                    Please ensure that you make your upload under the correct mode of study (<span style="color:blue"><b>Fulltime</b></span> or <span style="color:green"><b>Distance</b></span>) for each course. Also ensure that your Excel Sheet is correctly formatted and there are no error on the results.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5>Upload {{$assessmentType}}</h5>
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


