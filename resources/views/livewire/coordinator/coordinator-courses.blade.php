<div x-data="coordinatorCoursesStore()" class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Your Courses (Academic Year: {{ $academicYear }})</h5>
                        <div class="d-flex">
                            <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0 me-2" 
                                @click="exportToExcel()" 
                                :disabled="isLoading || isRefreshing">
                                <i class="fas fa-file-excel me-2"></i> Export to Excel
                            </button>
                            <button class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" 
                                wire:click="refreshData" 
                                :disabled="isRefreshing">
                                <i class="fas fa-sync-alt me-2" :class="{ 'spin': isRefreshing }"></i> Refresh
                            </button>
                        </div>
                    </div>
                    
                    @if($hasError)
                    <div class="alert alert-danger">
                        Failed to load courses data. Please refresh the page or contact support if the issue persists.
                        <button class="btn btn-sm btn-outline-danger ms-3" wire:click="refreshData">
                            <i class="fas fa-sync-alt me-1"></i> Try Again
                        </button>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <form action="{{ route('coordinator.exportBoardOfExaminersReport', ['basicInformationId' => encrypt($basicInformationId)]) }}" method="GET">
                                @csrf
                                <button type="submit" class="btn btn-info font-weight-bold py-2 px-4 rounded-0">Overall CA Report</button>
                            </form>
                        </div>

                        @if(auth()->user()->hasPermissionTo('Administrator'))
                        <form method="post" action="{{ route('admin.refreshCAInAprogram') }}">
                            <input type="hidden" name="studyId" value="{{ $studyId }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">
                                Refresh CAs
                            </button>
                        </form>
                        @endif
                        
                        <div>
                            <input type="text" id="courseSearchInput" 
                                x-model="searchTerm" 
                                @input="filterCourses()" 
                                placeholder="Search for courses.." 
                                class="shadow appearance-none border rounded py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-9">
                        </div>
                    </div>
                    
                    <!-- Loading State -->
                    <div x-show="isLoading && !hasResults" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading your courses...</p>
                    </div>
                    
                    <!-- Last updated timestamp -->
                    <div x-show="!isLoading && hasResults" class="text-right mb-2">
                        <small class="text-muted">
                            Last updated: <span x-text="formatLastUpdated(lastUpdated)"></span>
                        </small>
                    </div>
                    
                    <!-- Table -->
                    <div x-show="!isLoading && hasResults" style="overflow-x:auto;">
                        <table id="coursesTable" class="table table-hover">                        
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Course Name</th>
                                    <th scope="col">Course Code</th>
                                    <th scope="col">Programme Name</th>
                                    <th scope="col">Delivery Mode</th>
                                    <th scope="col">Year Of Study</th>
                                    <th scope="col">Number Of Uploads</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredResults.length === 0">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted">No courses found</p>
                                        </td>
                                    </tr>
                                </template>
                                
                                <template x-for="(course, index) in filteredResults" :key="course.ID">
                                    <tr>
                                        <td x-text="index + 1"></td>
                                        <td x-text="course.CourseDescription"></td>
                                        <td x-text="course.CourseName"></td>
                                        <td x-text="course.Name"></td>
                                        <td :style="{ color: course.Delivery === 'Fulltime' ? 'blue' : (course.Delivery === 'Distance' ? 'green' : 'black') }">
                                            <b x-text="course.Delivery"></b>
                                        </td>
                                        <td x-text="'Year ' + course.YearOfStudy"></td>
                                        <td>
                                            <form :action="getShowCaWithinRoute(course.ID)" method="GET">
                                                <input type="hidden" name="studyId" :value="course.StudyID">
                                                <input type="hidden" name="delivery" :value="course.Delivery">
                                                <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                                                    <span x-text="course.totalAssessments + ' assessments'"></span>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-right">
                                            <template x-if="isRegularCourse(course)">
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    <template x-if="hasCoordinatorPermission">
                                                        <button type="button" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0" 
                                                            @click="openUploadModal(course)"
                                                            :data-bs-target="'#uploadCourseModal' + course.ID + course.Delivery + course.StudyID">
                                                            Upload
                                                        </button>
                                                    </template>
                                                    
                                                    <button type="button" class="btn btn-success font-weight-bold py-2 px-4 rounded-0" 
                                                        @click="openViewModal(course)"
                                                        :data-bs-target="'#viewCourseModal' + course.ID + course.Delivery + course.StudyID">
                                                        View
                                                    </button>
                                                    
                                                    <form :action="getCourseCASettingsRoute(course)" method="GET" class="d-inline">
                                                        <input type="hidden" name="studyId" :value="course.StudyID">
                                                        <input type="hidden" name="hasComponents" value="0">
                                                        <input type="hidden" name="componentName" value="">
                                                        <button type="submit" class="btn btn-warning font-weight-bold py-2 px-4 rounded-0">
                                                            Settings
                                                        </button>
                                                    </form>
                                                </div>
                                            </template>
                                            
                                            <template x-if="isBasicSciencesCourse(course)">
                                                <div class="btn-group float-end" role="group" aria-label="Button group">
                                                    <form :action="getViewTotalCaInComponentCourseRoute(course)" method="GET" class="d-inline">
                                                        <input type="hidden" name="studyId" :value="course.StudyID">
                                                        <input type="hidden" name="isSettings" value="0">
                                                        <button type="submit" class="btn btn-secondary font-weight-bold py-2 px-4 rounded-0">
                                                            Total-CA
                                                        </button>
                                                    </form>
                                                    
                                                    <form :action="getViewCourseWithComponentsRoute(course, false)" method="GET" class="d-inline">
                                                        <input type="hidden" name="studyId" :value="course.StudyID">
                                                        <input type="hidden" name="isSettings" value="0">
                                                        <button type="submit" class="btn btn-info font-weight-bold py-2 px-4 rounded-0">
                                                            Proceed
                                                        </button>
                                                    </form>
                                                    
                                                    <form :action="getViewCourseWithComponentsRoute(course, true)" method="GET" class="d-inline">
                                                        <input type="hidden" name="studyId" :value="course.StudyID">
                                                        <input type="hidden" name="isSettings" value="1">
                                                        <button type="submit" class="btn btn-warning font-weight-bold py-2 px-4 rounded-0">
                                                            Components
                                                        </button>
                                                    </form>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include modals for each course -->
    @foreach($results as $result)
        @include('coordinator.components.uploadAssessmentTypeModal', ['result' => (object)$result])
        @include('coordinator.components.viewAssessmentTypeModal', ['result' => (object)$result])
    @endforeach

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .text-right {
            text-align: right;
        }
    </style>

    <script>
        function coordinatorCoursesStore() {
            return {
                results: @js($results),
                filteredResults: [],
                isLoading: @js($isLoading),
                hasError: @js($hasError),
                isRefreshing: @js($isRefreshing),
                lastUpdated: @js($lastUpdated),
                searchTerm: '',
                hasCoordinatorPermission: {{ auth()->user()->hasPermissionTo('Coordinator') ? 'true' : 'false' }},
                specialCourses: ['CAG201', 'GRA201', 'BCH2015', 'BCH2060', 'CBP2020', 'HAN2040', 'HAN2050', 'PGY2040', 'PHR3030', 'PHR3060', 'PTH2020', 'PTH2040', 'PTH2070'],
                
                get hasResults() {
                    return this.results && this.results.length > 0;
                },
                
                init() {
                    this.filteredResults = [...this.results];
                    
                    // Listen for Livewire events
                    Livewire.on('coursesUpdated', (data) => {
                        this.results = data.results;
                        this.filteredResults = [...this.results];
                        this.isLoading = false;
                        this.isRefreshing = false;
                        this.lastUpdated = data.lastUpdated;
                    });
                },
                
                filterCourses() {
                    if (!this.searchTerm) {
                        this.filteredResults = [...this.results];
                        return;
                    }
                    
                    const term = this.searchTerm.toLowerCase();
                    this.filteredResults = this.results.filter(course => 
                        course.CourseDescription.toLowerCase().includes(term) || 
                        course.CourseName.toLowerCase().includes(term) ||
                        course.Name.toLowerCase().includes(term)
                    );
                },
                
                formatLastUpdated(timestamp) {
                    if (!timestamp) return 'Never';
                    
                    // If timestamp is already formatted, return as is
                    if (typeof timestamp === 'string' && timestamp.includes('-')) {
                        return timestamp;
                    }
                    
                    const date = new Date(timestamp);
                    return date.toLocaleString();
                },
                
                isRegularCourse(course) {
                    return this.specialCourses.includes(course.CourseName) || 
                           course.Name.toUpperCase() !== 'BASIC SCIENCES';
                },
                
                isBasicSciencesCourse(course) {
                    return course.Name.toUpperCase() === 'BASIC SCIENCES';
                },
                
                openUploadModal(course) {
                    // This will trigger the Bootstrap modal
                    const modalId = `#uploadCourseModal${course.ID}${course.Delivery}${course.StudyID}`;
                    const modal = new bootstrap.Modal(document.querySelector(modalId));
                    modal.show();
                },
                
                openViewModal(course) {
                    // This will trigger the Bootstrap modal
                    const modalId = `#viewCourseModal${course.ID}${course.Delivery}${course.StudyID}`;
                    const modal = new bootstrap.Modal(document.querySelector(modalId));
                    modal.show();
                },
                
                getShowCaWithinRoute(courseId) {
                    return "{{ route('coordinator.showCaWithin', ':id') }}".replace(':id', btoa(courseId));
                },
                
                getCourseCASettingsRoute(course) {
                    return "{{ route('coordinator.courseCASettings', [
                        'courseIdValue' => ':courseId', 
                        'basicInformationId' => ':basicId', 
                        'delivery' => ':delivery'
                    ]) }}"
                    .replace(':courseId', btoa(course.ID))
                    .replace(':basicId', btoa(course.basicInformationId))
                    .replace(':delivery', btoa(course.Delivery));
                },
                
                getViewTotalCaInComponentCourseRoute(course) {
                    return "{{ route('coordinator.viewTotalCaInComponentCourse', [
                        'statusId' => ':statusId',
                        'courseIdValue' => ':courseId',
                        'basicInformationId' => ':basicId',
                        'delivery' => ':delivery'
                    ]) }}"
                    .replace(':statusId', btoa(course.caType || 0))
                    .replace(':courseId', btoa(course.ID))
                    .replace(':basicId', btoa(course.basicInformationId))
                    .replace(':delivery', btoa(course.Delivery));
                },
                
                getViewCourseWithComponentsRoute(course, isSettings) {
                    return "{{ route('coordinator.viewCourseWithComponents', [
                        'courseIdValue' => ':courseId',
                        'basicInformationId' => ':basicId',
                        'delivery' => ':delivery'
                    ]) }}"
                    .replace(':courseId', btoa(course.ID))
                    .replace(':basicId', btoa(course.basicInformationId))
                    .replace(':delivery', btoa(course.Delivery));
                },
                
                exportToExcel() {
                    if (!this.filteredResults.length) {
                        alert('No data to export');
                        return;
                    }
                    
                    // Check if XLSX is available
                    if (typeof XLSX === 'undefined') {
                        // Load the library if not available
                        const script = document.createElement('script');
                        script.src = 'https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js';
                        script.onload = () => this.performExport();
                        document.head.appendChild(script);
                    } else {
                        this.performExport();
                    }
                },
                
                performExport() {
                    // Prepare data for export
                    const exportData = this.filteredResults.map((course, index) => ({
                        '#': index + 1,
                        'Course Name': course.CourseDescription,
                        'Course Code': course.CourseName,
                        'Programme Name': course.Name,
                        'Delivery Mode': course.Delivery,
                        'Year Of Study': 'Year ' + course.YearOfStudy,
                        'Number Of Uploads': course.totalAssessments + ' assessments'
                    }));
                    
                    // Create worksheet
                    const ws = XLSX.utils.json_to_sheet(exportData);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Courses");
                    
                    // Generate filename
                    const fileName = `Coordinator_Courses_${new Date().toISOString().split('T')[0]}.xlsx`;
                    
                    // Export
                    XLSX.writeFile(wb, fileName);
                }
            };
        }
    </script>
</div>
