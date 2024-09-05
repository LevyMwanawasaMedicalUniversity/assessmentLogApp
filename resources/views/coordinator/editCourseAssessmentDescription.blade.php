<x-app-layout>
    <main id="main" class="main">
    <div class="pagetitle">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- {{ $results->CourseDescription }} - {{$results->CourseName}} --}}
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
                    <div class="card-body">
                        <h5 class="card-title">Edit CA</h5>

                        <!-- Vertical Form -->
                        <div class="container mx-auto px-4">
                            <div class="flex flex-wrap -mx-4">
                                <div class="w-full px-4">
                                    <div class="bg-white shadow rounded-lg p-6 border border-gray-300">
                                    <!-- Header -->
                                    <div class="mb-6">
                                        {{-- <h4 class="text-lg font-bold text-gray-800">Upload Excel Sheet Of Marks</h4> --}}
                                    </div>

                                    <!-- Alert Messages -->
                                    

                                    <!-- File Upload Form -->    
                                        <form action="{{ route('updateCourseAssessmentDescription',$result->course_assessments_id) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-light border rounded shadow-sm">
                                            @csrf

                                            <!-- Form Header -->
                                            <div class="mb-4">
                                                <h3 class="font-weight-bold text-primary">Update Course Description</h3>
                                                
                                            </div>

                                            <!-- Excel File Input -->
                                            <div class="form-group mb-4">
                                                <label for="description" class="font-weight-bold text-lg text-dark">Description</label>
                                                <textarea name="description" rows="2" cols="50" class="form-control" placeholder="Enter short assessment description here...">{{ $result->description ?? '' }}</textarea>

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
                                            <!-- Submit Button -->
                                            <div>
                                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                                    Update
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