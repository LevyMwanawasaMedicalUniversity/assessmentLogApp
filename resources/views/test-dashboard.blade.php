<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Test Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Test Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Simple Test Dashboard</h5>
                            <p>This is a simplified test dashboard to diagnose rendering issues.</p>
                            
                            <div class="mt-4">
                                <h6>Dashboard Variables:</h6>
                                <ul>
                                    <li>Students With CA: {{ $studentsWithCA ?? 'Not available' }}</li>
                                    <li>Total Courses Coordinated: {{ $totalCoursesCoordinated ?? 'Not available' }}</li>
                                    <li>Total Courses With CA: {{ $totalCoursesWithCA ?? 'Not available' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-app-layout>
