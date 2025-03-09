<x-app-layout>
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Students Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Students With CA</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $studentsWithCA ?? 0 }}</h6>
                                        <span class="text-success small pt-1 fw-bold">Active Students</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Students Card -->

                    <!-- Courses Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Courses</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalCoursesCoordinated ?? 0 }}</h6>
                                        <span class="text-primary small pt-1 fw-bold">With Coordinators</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Courses Card -->

                    <!-- CA Courses Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">Courses With CA</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalCoursesWithCA ?? 0 }}</h6>
                                        <span class="text-success small pt-1 fw-bold">With Assessments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End CA Courses Card -->

                    <!-- Recent Activity -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Activity</h5>
                                <div class="activity">
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">Now</div>
                                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                        <div class="activity-content">
                                            Dashboard updated successfully
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Recent Activity -->
                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">
                <!-- Quick Links -->
                <div class="card">
                    <div class="card-body pb-0">
                        <h5 class="card-title">Quick Links</h5>
                        <div class="list-group">
                            @if(auth()->user()->hasPermissionTo('Coordinator'))
                                <a href="{{ route('pages.upload') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="bi bi-upload me-2"></i> Upload CA
                                </a>
                                <a href="{{ route('pages.uploadFinalExam') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text me-2"></i> Upload Final Exam
                                </a>
                            @endif
                            
                            @if(auth()->user()->hasPermissionTo('Registrar'))
                                <a href="{{ route('admin.viewDeans') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="bi bi-people me-2"></i> View Deans
                                </a>
                                <a href="{{ route('admin.viewCoordinators') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="bi bi-person-badge me-2"></i> View Coordinators
                                </a>
                            @endif
                            
                            @if(auth()->user()->hasPermissionTo('Administrator'))
                                <a href="{{ route('admin.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="bi bi-gear me-2"></i> Admin Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                </div><!-- End Quick Links -->
            </div><!-- End Right side columns -->
        </div>
    </section>
</x-app-layout>
