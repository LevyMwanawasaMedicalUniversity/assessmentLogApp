<div class="card info-card revenue-card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Courses From LM-MAX <span>| Total</span></h5>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshCoursesFromLmmax()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
    <div id="courses-from-lmmax-container">
        <div class="content-container">
            @if (auth()->user()->hasPermissionTo('Registrar'))
                <a href="{{ route('coordinator.viewOnlyProgrammesWithCa') }}">
            @endif
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: #ff771d;">
                        <i class="bi bi-journal-text text-white"></i>
                    </div>
                    <div class="ps-3">
                        <h6 id="total-courses-with-ca">0</h6>
                        <span class="small pt-1 fw-bold" style="color: #ff771d;">With Continuous</span> <span class="text-muted small pt-2 ps-1">Assessments</span>
                    </div>
                </div>
            @if (auth()->user()->hasPermissionTo('Registrar'))
                </a>
            @endif
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>
</div>

<script>
// Function to fetch courses from LMMAX data
document.addEventListener('DOMContentLoaded', function() {
    fetchCoursesFromLmmax();
});

function fetchCoursesFromLmmax() {
    const container = document.getElementById('courses-from-lmmax-container');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const totalCoursesWithCa = document.getElementById('total-courses-with-ca');
    
    // Show loading state
    totalCoursesWithCa.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Loading...</span>
        </div>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.courses-from-lmmax') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                totalCoursesWithCa.textContent = data.totalCoursesWithCa;
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses from LMMAX:', err);
            error.classList.remove('d-none');
            totalCoursesWithCa.textContent = '0';
        });
}

// Function to refresh data
function refreshCoursesFromLmmax() {
    fetchCoursesFromLmmax();
}
</script>

{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
