<div class="card info-card revenue-card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Students With CA <span>| Total</span></h5>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshStudentsWithCa()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="students-with-ca-container">
            <div class="content-container">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary">
                        <i class="bi bi-people text-white"></i>
                    </div>
                <div class="ps-3">
                    <h6 id="total-students-with-ca">0</h6>
                    <span class="text-primary small pt-1 fw-bold">With Continuous</span> <span class="text-muted small pt-2 ps-1">Assessments</span>
                </div>
            </div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>
</div>

<script>
// Function to fetch students with CA data
document.addEventListener('DOMContentLoaded', function() {
    fetchStudentsWithCa();
});

function fetchStudentsWithCa() {
    const container = document.getElementById('students-with-ca-container');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const totalStudentsWithCa = document.getElementById('total-students-with-ca');
    
    // Show loading state
    totalStudentsWithCa.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Loading...</span>
        </div>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.students-with-ca') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Students With CA API Response:', data); 
            if (data.status === 'success') {
                // Check for all possible field names in the API response
                const count = data.totalStudentsWithCa !== undefined ? data.totalStudentsWithCa : 
                             (data.studentCount !== undefined ? data.studentCount : 0);
                
                // Format the number with commas for thousands
                const formattedCount = new Intl.NumberFormat().format(count);
                totalStudentsWithCa.textContent = formattedCount;
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching students with CA:', err);
            error.classList.remove('d-none');
            totalStudentsWithCa.textContent = '0';
        });
}

// Function to refresh data
function refreshStudentsWithCa() {
    fetchStudentsWithCa();
}
</script>

{{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
