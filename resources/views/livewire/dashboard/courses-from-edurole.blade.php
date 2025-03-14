<div class="card info-card revenue-card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Courses from Edurole <span>| Total</span></h5>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshCoursesFromEdurole()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="courses-from-edurole-container">
            <div class="content-container">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success">
                        <i class="bi bi-calendar2-week text-white"></i>
                    </div>
                    <div class="ps-3">
                        <h6 id="total-courses-coordinated">0</h6>
                        <span class="text-success small pt-1 fw-bold">With Coordinators</span> <span class="text-muted small pt-2 ps-1">Assigned</span>
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
// Function to fetch courses from Edurole data
document.addEventListener('DOMContentLoaded', function() {
    fetchCoursesFromEdurole();
});

function fetchCoursesFromEdurole() {
    const container = document.getElementById('courses-from-edurole-container');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const totalCoursesCoordinated = document.getElementById('total-courses-coordinated');
    
    // Show loading state
    totalCoursesCoordinated.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Loading...</span>
        </div>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.courses-from-edurole') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                totalCoursesCoordinated.textContent = data.totalCoursesCoordinated;
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses from Edurole:', err);
            error.classList.remove('d-none');
            totalCoursesCoordinated.textContent = '0';
        });
}

// Function to refresh data
function refreshCoursesFromEdurole() {
    fetchCoursesFromEdurole();
}
</script>
