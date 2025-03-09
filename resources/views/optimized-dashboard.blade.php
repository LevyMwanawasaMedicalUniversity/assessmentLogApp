<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-3">
                        <h5 class="card-title">Assessment Log Dashboard</h5>
                        <p class="text-muted">Welcome to the Assessment Log App dashboard</p>
                        
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i> Dashboard loaded successfully
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards - Using 2-column layout for efficiency -->
        <div class="row">
            <!-- Students Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card info-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Students With CA</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="mb-0">{{ $studentsWithCA ?? 0 }}</h6>
                                <span class="text-success small pt-1 fw-bold">From Assessment System</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Courses Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card info-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Total Courses</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="mb-0">{{ $totalCoursesCoordinated ?? 0 }}</h6>
                                <span class="text-primary small pt-1 fw-bold">With Coordinators</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CA Courses Card -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card info-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Courses With CA</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="mb-0">{{ $totalCoursesWithCA ?? 0 }}</h6>
                                <span class="text-success small pt-1 fw-bold">With Assessments</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity - Compact Layout -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
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
            </div>
        </div>
    </div>
</x-app-layout>
