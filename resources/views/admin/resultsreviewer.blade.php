<x-app-layout>

    <main id="main" class="main">
        <div class="pagetitle">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span>
                <b>EXAMINATION GRADES REVIEWER</b></span>
            </h2>
            @include('layouts.alerts')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                    Please ensure that the excel sheet is formated corrected.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <nav>
                {{-- {{ Breadcrumbs::render() }} --}}
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                        <div class="card"><div class="card-header text-white">                        
                            <h5><b>UPLOAD</b></h5>
                        </div>
                        <div class="card-body">
                            <!-- Vertical Form -->
                            <div class="container px-4">
                                <div class="row pt-3">
                                    <div class="col-12">
                                        <div class="bg-white shadow rounded-lg p-6 border border-gray-300">
                                            <!-- Header -->
                                            <div class="mb-6">
                                                {{-- <h4 class="text-lg font-bold text-gray-800">Upload Excel Sheet Of Marks</h4> --}}
                                            </div>

                                            <!-- Alert Messages -->
                                            

                                            <!-- File Upload Form -->    
                                                <form action="{{ route('admin.importGradesForReview') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-light border rounded shadow-sm">
                                                    @csrf

                                                    <!-- Form Header -->
                                                    <div class="mb-4">
                                                        <h3 class="font-weight-bold">
                                                            UPLOAD FILE FOR REVIEW
                                                        </h3>
                                                        <p class="text-muted">Please imort the file for Grades review.</p>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label for="excelFile" class="font-weight-bold text-lg text-dark">Choose "Excel Workbook" (xlsx) File</label>
                                                        <input type="file" name="excelFile" accept=".xlsx, .xlsm" class="form-control-file" id="excelFileInput" required>
                                                        <small class="form-text text-muted">Accepted formats: .xlsx, .xlsm</small>
                                                    </div>

                                                    <!-- File Preview -->
                                                    <div class="form-group mb-4 d-none" id="filePreview"></div>

                                                    <!-- Loader -->
                                                    <div class="form-group mb-4 d-none text-center" id="loader">
                                                        <div id="percentage" class="text-muted">0%</div>
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p>Loading...</p>
                                                    </div>
                                                    <!-- Academic Year Dropdown -->
                                                    <div class="form-group mb-6">
                                                        <label for="academicYear" class="font-weight-bold text-lg text-dark">Academic Year</label>
                                                        <select name="academicYear" class="form-control w-auto" required>
                                                            @for ($year = 2019; $year <= date('Y'); $year++)
                                                                <option value="{{ $year }}" {{ $year == 2024 ? 'selected' : '' }}>{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <!-- Submit Button -->
                                                    <div>
                                                        <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                            REVIEW GRADES
                                                        </button>
                                                    </div>
                                                </form>
                                        </div>
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


