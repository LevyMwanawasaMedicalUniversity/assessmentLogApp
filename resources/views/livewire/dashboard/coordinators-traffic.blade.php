<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Coordinators Traffic</h4>
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
                            <th>Coordinator</th>
                            <th>Last Login</th>
                            <th>Last Activity</th>
                            <th>Status</th>
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
            <td colspan="4" class="text-center">
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
            if (data.status === 'success') {
                // Clear the table body
                tableBody.innerHTML = '';
                
                if (data.coordinators && data.coordinators.length > 0) {
                    // Populate the table with coordinators traffic data
                    data.coordinators.forEach(coordinator => {
                        const row = document.createElement('tr');
                        
                        // Calculate status based on last activity
                        let status = 'Inactive';
                        let statusClass = 'text-danger';
                        
                        if (coordinator.last_activity) {
                            const lastActivity = new Date(coordinator.last_activity);
                            const now = new Date();
                            const diffInDays = Math.floor((now - lastActivity) / (1000 * 60 * 60 * 24));
                            
                            if (diffInDays < 1) {
                                status = 'Active Today';
                                statusClass = 'text-success';
                            } else if (diffInDays < 7) {
                                status = 'Active This Week';
                                statusClass = 'text-warning';
                            }
                        }
                        
                        row.innerHTML = `
                            <td>${coordinator.name}</td>
                            <td>${coordinator.last_login ? new Date(coordinator.last_login).toLocaleString() : 'Never'}</td>
                            <td>${coordinator.last_activity ? new Date(coordinator.last_activity).toLocaleString() : 'Never'}</td>
                            <td class="${statusClass}">${status}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    // No coordinators found
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="4" class="text-center">No coordinators found</td>
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
                    <td colspan="4" class="text-center text-danger">
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
