<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Courses With CA Per Programme</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshCoursesWithCaPerProgramme()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportCoursesWithCaPerProgrammeToCSV()">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>Programme</th>
                            <th>Courses with CA</th>
                            <th>Total Courses</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody id="courses-with-ca-per-programme-table-body">
                        <!-- Loader will be shown instead of this content -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>

<script>
// Function to fetch courses with CA per programme data
document.addEventListener('DOMContentLoaded', function() {
    fetchCoursesWithCaPerProgramme();
});

function fetchCoursesWithCaPerProgramme() {
    const error = document.querySelector('.error-container');
    const tableBody = document.getElementById('courses-with-ca-per-programme-table-body');
    
    // Clear the table body and add a loading row
    tableBody.innerHTML = `
        <tr>
            <td colspan="4" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading programme data...
            </td>
        </tr>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Courses With CA Per Programme API Response:', data); 
            if (data.status === 'success') {
                // Clear the table body
                tableBody.innerHTML = '';
                
                // Check both possible field names in the API response
                const programmes = data.coursesWithCaPerProgramme || data.courseWithCaPerProgramme || [];
                
                if (programmes && programmes.length > 0) {
                    // Populate the table with courses with CA per programme data
                    programmes.forEach(programme => {
                        // Support both field naming conventions
                        const coursesWithCa = programme.courses_with_ca || programme.assessment_count || 0;
                        const totalCourses = programme.total_courses || 0;
                        const programmeName = programme.programme_name || '';
                        
                        const percentage = totalCourses > 0 
                            ? ((coursesWithCa / totalCourses) * 100).toFixed(2) 
                            : '0.00';
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${programmeName}</td>
                            <td>${coursesWithCa}</td>
                            <td>${totalCourses}</td>
                            <td>${percentage}%</td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    // No data found
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="4" class="text-center">No data available</td>
                    `;
                    tableBody.appendChild(row);
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses with CA per programme:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
        });
}

// Function to refresh data
function refreshCoursesWithCaPerProgramme() {
    fetchCoursesWithCaPerProgramme();
}

// Function to export data to CSV
function exportCoursesWithCaPerProgrammeToCSV() {
    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
        .then(response => response.json())
        .then(data => {
            // Check both possible field names in the API response
            const programmes = data.coursesWithCaPerProgramme || data.courseWithCaPerProgramme || [];
            
            if (data.status === 'success' && programmes && programmes.length > 0) {
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "Programme,Courses with CA,Total Courses,Percentage\n";
                
                programmes.forEach(programme => {
                    // Support both field naming conventions
                    const coursesWithCa = programme.courses_with_ca || programme.assessment_count || 0;
                    const totalCourses = programme.total_courses || 0;
                    const programmeName = programme.programme_name || '';
                    
                    const percentage = totalCourses > 0 
                        ? ((coursesWithCa / totalCourses) * 100).toFixed(2) 
                        : '0.00';
                    
                    csvContent += `${programmeName},${coursesWithCa},${totalCourses},${percentage}%\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "courses_with_ca_per_programme.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('No data available to export');
            }
        })
        .catch(err => {
            console.error('Error exporting data:', err);
            alert('Failed to export data');
        });
}
</script>
