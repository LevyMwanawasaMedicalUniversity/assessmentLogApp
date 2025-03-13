<div class="card info-card revenue-card">
    <div class="card-body">
        <h5 class="card-title">Courses from Edurole <span>| Total</span></h5>

        <div id="courses-from-edurole-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>
                    <div class="ps-3">
                        <h6 id="total-courses-coordinated">0</h6>
                        <span class="text-primary small pt-1 fw-bold">With Coordinators Assigned</span>
                    </div>
                </div>
            </div>
            
            <div class="error-container d-none">
                <div class="alert alert-danger">
                    Failed to load data. Please refresh the page.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('courses-from-edurole-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const totalCoursesCoordinated = document.getElementById('total-courses-coordinated');
    
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
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses from Edurole:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>
