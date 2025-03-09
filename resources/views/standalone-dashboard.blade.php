<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Log Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f6f9ff;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(1, 41, 112, 0.1);
            border: none;
        }
        .card-icon {
            width: 50px;
            height: 50px;
            background-color: #f6f6fe;
            border-radius: 50%;
            font-size: 24px;
            color: #4154f1;
        }
        .dashboard-title {
            color: #012970;
            font-weight: 700;
        }
        .card-title {
            color: #012970;
            font-size: 15px;
            font-weight: 500;
            padding: 0;
            margin-bottom: 15px;
        }
        .compact-text {
            font-size: 0.9rem;
        }
        .stats-value {
            font-size: 28px;
            font-weight: 700;
            color: #012970;
            margin-bottom: 0;
        }
        .activity-badge {
            color: #4154f1;
            font-size: 16px;
            margin-right: 10px;
        }
        .activity-item {
            margin-bottom: 15px;
        }
        .activity-label {
            width: 80px;
            color: #899bbd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="dashboard-title">Assessment Log Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <!-- Statistics Cards - Using 3-column layout for efficiency -->
        <div class="row g-3 mb-4">
            <!-- Students Card -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Students With CA</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <h2 class="stats-value">{{ $studentsWithCA ?? 0 }}</h2>
                                <span class="text-success small fw-bold">From Assessment System</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Courses Card -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Total Courses</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div>
                                <h2 class="stats-value">{{ $totalCoursesCoordinated ?? 0 }}</h2>
                                <span class="text-primary small fw-bold">With Coordinators</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CA Courses Card -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Courses With CA</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div>
                                <h2 class="stats-value">{{ $totalCoursesWithCA ?? 0 }}</h2>
                                <span class="text-success small fw-bold">With Assessments</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity - Compact Layout -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Recent Activity</h5>
                        <div class="activity">
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-label">Now</div>
                                <i class="bi bi-circle-fill activity-badge"></i>
                                <div class="compact-text">Dashboard updated successfully</div>
                            </div>
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-label">Today</div>
                                <i class="bi bi-circle-fill activity-badge"></i>
                                <div class="compact-text">System maintenance completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="card-title">Quick Links</h5>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-people me-2"></i>
                                <span class="compact-text">View Students</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-journal-text me-2"></i>
                                <span class="compact-text">Manage Courses</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-clipboard-check me-2"></i>
                                <span class="compact-text">Assessment Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="text-muted small">Assessment Log App © {{ date('Y') }}</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
