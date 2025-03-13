<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Deans Per School</h4>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshDeansPerSchool()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>School</th>
                            <th>Dean</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="deans-per-school-table-body">
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
// Function to fetch deans per school data
document.addEventListener('DOMContentLoaded', function() {
    fetchDeansPerSchool();
});

function fetchDeansPerSchool() {
    const error = document.querySelector('.error-container');
    const tableBody = document.getElementById('deans-per-school-table-body');
    
    // Clear the table body and add a loading row
    tableBody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading deans data...
            </td>
        </tr>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.deans-per-school') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Deans Per School API Response:', data); 
            if (data.status === 'success') {
                // Clear the table body
                tableBody.innerHTML = '';
                
                if (data.deans && data.deans.length > 0) {
                    // Populate the table with deans data
                    data.deans.forEach(dean => {
                        const row = document.createElement('tr');
                        // Support both field naming conventions
                        const schoolName = dean.Description || dean.school_name || dean.school || '';
                        const deanName = (dean.FirstName && dean.Surname) ? 
                            `${dean.FirstName} ${dean.Surname}` : 
                            (dean.dean_name || dean.dean || '');
                        const email = dean.PrivateEmail || dean.email || 'N/A';
                        
                        row.innerHTML = `
                            <td>${schoolName}</td>
                            <td>${deanName}</td>
                            <td>${email}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    // No deans found
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="text-center">No deans found</td>
                    `;
                    tableBody.appendChild(row);
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching deans per school:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
        });
}

// Function to refresh data
function refreshDeansPerSchool() {
    fetchDeansPerSchool();
}
</script>
