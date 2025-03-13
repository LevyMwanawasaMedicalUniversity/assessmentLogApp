<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coordinators</h1>
        @include('layouts.alerts')
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Coordinators</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title" id="coordinatorsTitle">Coordinators @isset($schoolId) in {{$results->first()->SchoolName}} @else on Edurole @endif</h5>
                            <div class=""> 
                                <button class="btn btn-info font-weight-bold py-2 px-4 rounded-0" id="exportBtn">Export to Excel</button>
                                @if (auth()->user()->hasPermissionTo('Administrator'))
                                <form method="POST" action="{{ route('admin.importCoordinators') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                        Import Coordinators
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Loading spinner -->
                        <div id="loadingSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading coordinators data...</p>
                        </div>
                        
                        <!-- Error message container -->
                        <div id="errorContainer" class="alert alert-danger d-none">
                            Failed to load coordinators data. Please refresh the page.
                        </div>
                        
                        <div id="coordinatorsTableContainer" class="d-none">
                            <div style="overflow-x:auto;">
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Last Name</th>
                                            <th scope="col">Programme</th>
                                            <th scope="col">School</th>
                                            <th scope="col">Last Login</th>
                                            <th scope="col">Courses</th>
                                            <th scope="col">Courses With CA</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coordinatorsTableBody">
                                        <!-- Table content will be loaded dynamically via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- End Table with hoverable rows -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load coordinators data
        fetchCoordinatorsData();
        
        // Set up export button
        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable');
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
            XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
        });
    });
    
    function fetchCoordinatorsData() {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorContainer = document.getElementById('errorContainer');
        const tableContainer = document.getElementById('coordinatorsTableContainer');
        const tableBody = document.getElementById('coordinatorsTableBody');
        const titleElement = document.getElementById('coordinatorsTitle');
        
        // Show loading spinner
        loadingSpinner.classList.remove('d-none');
        errorContainer.classList.add('d-none');
        tableContainer.classList.add('d-none');
        
        // Get schoolId from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        const schoolId = urlParams.get('schoolId');
        
        // Store route URLs for use in the table
        const routes = {
            viewOnlyProgrammesWithCaForCoordinator: "{{ route('coordinator.viewOnlyProgrammesWithCaForCoordinator', ':id') }}",
            uploadFinalExamAndCa: "{{ route('pages.uploadFinalExamAndCa') }}",
            viewCoordinatorsCourses: "{{ route('admin.viewCoordinatorsCourses', ':id') }}"
        };
        
        // Fetch data from API
        fetch(`{{ route('api.coordinators.data') }}${schoolId ? '?schoolId=' + schoolId : ''}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Coordinators Data API Response:', data);
                if (data.status === 'success') {
                    // Update the title if schoolId is present
                    if (data.schoolId) {
                        // We'll need to fetch the school name separately or include it in the API response
                        // For now, we'll keep the existing title
                    }
                    
                    // Populate the table
                    populateCoordinatorsTable(data.coordinators, routes);
                    
                    // Hide loading spinner and show table
                    loadingSpinner.classList.add('d-none');
                    tableContainer.classList.remove('d-none');
                } else {
                    throw new Error('Data status is not success');
                }
            })
            .catch(err => {
                console.error('Error fetching coordinators data:', err);
                loadingSpinner.classList.add('d-none');
                errorContainer.classList.remove('d-none');
            });
    }
    
    function populateCoordinatorsTable(coordinators, routes) {
        const tableBody = document.getElementById('coordinatorsTableBody');
        tableBody.innerHTML = ''; // Clear existing content
        
        // Ensure coordinators is an array
        const coordinatorsArray = Array.isArray(coordinators) ? coordinators : Object.values(coordinators);
        
        coordinatorsArray.forEach((coordinator, index) => {
            const row = document.createElement('tr');
            
            // Create route URLs with proper IDs
            const viewProgrammesRoute = routes.viewOnlyProgrammesWithCaForCoordinator.replace(':id', coordinator.id);
            const viewCoordinatorsCoursesRoute = routes.viewCoordinatorsCourses.replace(':id', coordinator.encrypted_id);
            
            // Create row content
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${coordinator.firstname}</td>
                <td>${coordinator.surname}</td>
                <td>${coordinator.name}</td>
                <td>${coordinator.school}</td>
                <td style="color: ${coordinator.last_login !== 'NEVER' ? 'blue' : 'red'};">
                    ${coordinator.last_login}
                </td>
                <td>${coordinator.numberOfCourses} Courses</td>
                <td>
                    <form action="${viewProgrammesRoute}" method="GET">
                        <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                            ${coordinator.coursesWithCa} Courses
                        </button>
                    </form>
                </td>
                <td>
                    <div class="btn-group float-end" role="group" aria-label="Button group">
                        ${hasAdminPermission() ? `
                        <form method="GET" action="${routes.uploadFinalExamAndCa}">
                            <input type="hidden" name="basicInformationId" value="${coordinator.encrypted_id}">
                            <button type="submit" class="btn btn-success font-weight-bold py-2 px-4 rounded-0">
                                Final Exam
                            </button>
                        </form>
                        ` : ''}
                        <form method="GET" action="${viewCoordinatorsCoursesRoute}">
                            <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">
                                Continuous Assessment
                            </button>
                        </form>
                    </div>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
    }
    
    // Helper functions for permissions
    function hasAdminPermission() {
        return {{ auth()->user()->hasPermissionTo('Administrator') ? 'true' : 'false' }};
    }
</script>
</x-app-layout>
