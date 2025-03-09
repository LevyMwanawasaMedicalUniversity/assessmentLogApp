import React from 'react';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth, studentsWithCA, totalCoursesCoordinated, totalCoursesWithCA, userPermissions }) {
    // Extract permissions from the data
    const { isAdmin, isCoordinator, isDean, isRegistrar } = userPermissions || {
        isAdmin: false,
        isCoordinator: false,
        isDean: false,
        isRegistrar: false
    };
    
    return (
        <>
            <Head title="Dashboard" />
            
            <div className="pagetitle">
                <h1>Dashboard</h1>
                <nav>
                    <ol className="breadcrumb">
                        <li className="breadcrumb-item"><a href="/">Home</a></li>
                        <li className="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <section className="section dashboard">
                <div className="row g-3">
                    {/* Left side columns */}
                    <div className="col-lg-8">
                        <div className="row g-3">
                            {/* Stats Cards - Using 3-column layout for metadata to optimize space */}
                            <div className="col-xxl-4 col-md-4">
                                <div className="card info-card sales-card h-100">
                                    <div className="card-body p-3">
                                        <h5 className="card-title text-sm mb-2">Students With CA</h5>
                                        <div className="d-flex align-items-center">
                                            <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i className="bi bi-people"></i>
                                            </div>
                                            <div>
                                                <h6 className="mb-0 fs-5">{studentsWithCA || 0}</h6>
                                                <span className="text-success small fw-bold">Active Students</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Courses Card */}
                            <div className="col-xxl-4 col-md-4">
                                <div className="card info-card revenue-card h-100">
                                    <div className="card-body p-3">
                                        <h5 className="card-title text-sm mb-2">Total Courses</h5>
                                        <div className="d-flex align-items-center">
                                            <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i className="bi bi-journal-text"></i>
                                            </div>
                                            <div>
                                                <h6 className="mb-0 fs-5">{totalCoursesCoordinated || 0}</h6>
                                                <span className="text-primary small fw-bold">With Coordinators</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* CA Courses Card */}
                            <div className="col-xxl-4 col-md-4">
                                <div className="card info-card customers-card h-100">
                                    <div className="card-body p-3">
                                        <h5 className="card-title text-sm mb-2">Courses With CA</h5>
                                        <div className="d-flex align-items-center">
                                            <div className="card-icon rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i className="bi bi-clipboard-check"></i>
                                            </div>
                                            <div>
                                                <h6 className="mb-0 fs-5">{totalCoursesWithCA || 0}</h6>
                                                <span className="text-success small fw-bold">With Assessments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Recent Activity - Optimized to reduce padding and use smaller text */}
                            <div className="col-12">
                                <div className="card">
                                    <div className="card-body p-3">
                                        <h5 className="card-title text-sm mb-2">Recent Activity</h5>
                                        <div className="activity">
                                            <div className="activity-item d-flex">
                                                <div className="activite-label text-sm">Now</div>
                                                <i className='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                                <div className="activity-content text-sm text-truncate">
                                                    Dashboard updated successfully
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right side columns */}
                    <div className="col-lg-4">
                        {/* Quick Links - Optimized with reduced padding */}
                        <div className="card h-100">
                            <div className="card-body p-3">
                                <h5 className="card-title text-sm mb-2">Quick Links</h5>
                                <div className="list-group list-group-flush">
                                    {isCoordinator && (
                                        <>
                                            <a href="/pages/upload" className="list-group-item list-group-item-action py-2 px-3 border-0">
                                                <i className="bi bi-upload me-2"></i>
                                                <span className="text-sm">Upload CA</span>
                                            </a>
                                            <a href="/pages/upload-final-exam" className="list-group-item list-group-item-action py-2 px-3 border-0">
                                                <i className="bi bi-file-earmark-text me-2"></i>
                                                <span className="text-sm">Upload Final Exam</span>
                                            </a>
                                        </>
                                    )}
                                    
                                    {isRegistrar && (
                                        <>
                                            <a href="/admin/view-deans" className="list-group-item list-group-item-action py-2 px-3 border-0">
                                                <i className="bi bi-people me-2"></i>
                                                <span className="text-sm">View Deans</span>
                                            </a>
                                            <a href="/admin/view-coordinators" className="list-group-item list-group-item-action py-2 px-3 border-0">
                                                <i className="bi bi-person-badge me-2"></i>
                                                <span className="text-sm">View Coordinators</span>
                                            </a>
                                        </>
                                    )}
                                    
                                    {isAdmin && (
                                        <a href="/admin" className="list-group-item list-group-item-action py-2 px-3 border-0">
                                            <i className="bi bi-gear me-2"></i>
                                            <span className="text-sm">Admin Dashboard</span>
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
