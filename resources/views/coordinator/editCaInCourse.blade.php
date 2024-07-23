<x-app-layout>

    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $results->CourseDescription }} - {{$results->CourseName}}
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
                        <div class="card-header bg-primary text-white">
                            <h5>Edit CA</h5>
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