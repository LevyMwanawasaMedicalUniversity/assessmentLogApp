<x-app-layout>

    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"> 
                <span style="color: {{ $delivery == 'Fulltime' ? 'blue' : ($delivery == 'Distance' ? 'green' : 'black') }}">
                <b>{{ $results->CourseDescription }} - {{$results->CourseName}} : {{$delivery}} {{$courseAssessment->assesment_type_name}} @if($hasComponents) in {{$hasComponents}}@endif</b>
                
                </span>
            </h2>
            @include('layouts.alerts')
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        {{-- <div class="card-header bg-primary text-white">
                            <h5>Edit CA</h5>
                        </div> --}}
                        <div class="card"><div class="card-header {{ $delivery == 'Fulltime' ? 'bg-primary' : ($delivery == 'Distance' ? 'bg-success' : '') }} text-white">                        
                            <h5><b>Edit CA {{$courseAssessment->assesment_type_name}} ( {{$delivery}} Education ) @isset($hasComponents) in {{$hasComponents}} @endisset</b></h5>
                        </div>
                        <div class="card-body">
                            <!-- Vertical Form -->
                            <div class="container px-4">
                                <div class="row pt-3">
                                    <div class="col-12">
                                        @include('coordinator.components.editExcelSheetForm')
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