<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Coordinators Per School</h4>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshCoordinatorsTraffic()">
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
                            <th>Description</th>
                            <th>Coordinators Count</th>
                        </tr>
                    </thead>
                    <tbody id="coordinators-traffic-table-body">
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
// Function to fetch coordinators traffic data
document.addEventListener('DOMContentLoaded', function() {
    fetchCoordinatorsTraffic();
});

function fetchCoordinatorsTraffic() {
    const error = document.querySelector('.error-container');
    const tableBody = document.getElementById('coordinators-traffic-table-body');
    
    // Clear the table body and add a loading row
    tableBody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading coordinator data...
            </td>
        </tr>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.coordinators-traffic') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Coordinators Traffic API Response:', data);
            if (data.status === 'success') {
                // Clear the table body
                tableBody.innerHTML = '';
                
                if (data.coordinatorsPerSchool && data.coordinatorsPerSchool.length > 0) {
                    // Populate the table with coordinators per school data
                    data.coordinatorsPerSchool.forEach(school => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${school.school_name || 'N/A'}</td>
                            <td>${school.school_description || 'N/A'}</td>
                            <td><strong>${school.coordinator_count || 0}</strong></td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    // No data found
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="text-center">No coordinator data found</td>
                    `;
                    tableBody.appendChild(row);
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching coordinators traffic:', err);
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
function refreshCoordinatorsTraffic() {
    fetchCoordinatorsTraffic();
}
</script>
