<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">CA PER SCHOOL</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshCaPerSchool()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportCaPerSchoolToCSV()">
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
                            <th>School</th>
                            <th>Courses with CA</th>
                            <th>Total Courses</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody id="ca-per-school-table-body">
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
// Function to fetch CA per school data
document.addEventListener('DOMContentLoaded', function() {
    fetchCaPerSchool();
});

function fetchCaPerSchool() {
    const error = document.querySelector('.error-container');
    const tableBody = document.getElementById('ca-per-school-table-body');
    
    // Clear the table body and add a loading row
    tableBody.innerHTML = `
        <tr>
            <td colspan="4" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading school data...
            </td>
        </tr>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.ca-per-school') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Clear the table body
                tableBody.innerHTML = '';
                
                if (data.caPerSchool && data.caPerSchool.length > 0) {
                    // Populate the table with CA per school data
                    data.caPerSchool.forEach(school => {
                        const percentage = school.total_courses > 0 
                            ? ((school.courses_with_ca / school.total_courses) * 100).toFixed(2) 
                            : '0.00';
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${school.school_name}</td>
                            <td>${school.courses_with_ca}</td>
                            <td>${school.total_courses}</td>
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
            console.error('Error fetching CA per school:', err);
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
function refreshCaPerSchool() {
    fetchCaPerSchool();
}

// Function to export data to CSV
function exportCaPerSchoolToCSV() {
    fetch('{{ route('api.dashboard.ca-per-school') }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.caPerSchool && data.caPerSchool.length > 0) {
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "School,Courses with CA,Total Courses,Percentage\n";
                
                data.caPerSchool.forEach(school => {
                    const percentage = school.total_courses > 0 
                        ? ((school.courses_with_ca / school.total_courses) * 100).toFixed(2) 
                        : '0.00';
                    
                    csvContent += `${school.school_name},${school.courses_with_ca},${school.total_courses},${percentage}%\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "ca_per_school.csv");
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
