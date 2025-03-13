<x-app-layout>
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Dashboard</h1>
            @include('layouts.alerts')
            <nav>
                {{ Breadcrumbs::render() }}
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Students With CA Card -->
                        <div class="col-xxl-4 col-md-4">
                            @livewire('dashboard.students-with-ca')
                        </div><!-- End Students With CA Card -->

                        <!-- Courses from Edurole Card -->
                        <div class="col-xxl-4 col-md-4">
                            @livewire('dashboard.courses-from-edurole')
                        </div><!-- End Courses from Edurole Card -->

                        <!-- Courses From LM-MAX Card -->
                        <div class="col-xxl-4 col-md-4">
                            @livewire('dashboard.courses-from-lmmax')
                        </div><!-- End Courses From LM-MAX Card -->

                        <!-- Course With CA Per Programme -->
                        <div class="col-12">
                            @livewire('dashboard.course-with-ca-per-programme')
                        </div><!-- End Course With CA Per Programme -->

                        <!-- Deans Per School -->
                        <div class="col-12">
                            @livewire('dashboard.deans-per-school')
                        </div><!-- End Deans Per School -->

                    </div>
                </div><!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-4">

                    <!-- CA Per School Chart -->
                    <div class="col-12">
                        @livewire('dashboard.ca-per-school')
                    </div><!-- End CA Per School Chart -->

                    <!-- Coordinators Traffic Chart -->
                    <div class="col-12 mt-4">
                        @livewire('dashboard.coordinators-traffic')
                    </div><!-- End Coordinators Traffic Chart -->

                </div><!-- End Right side columns -->

            </div>
        </section>

    </main><!-- End #main -->
</x-app-layout>
