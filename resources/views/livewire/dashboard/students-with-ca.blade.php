<div class="card info-card sales-card">                   
    <div class="card-body">
        <h5 class="card-title">Students With CA <span>| Uploaded</span></h5>

        <div id="students-with-ca-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                        <h6 id="student-count">0</h6>
                        <span class="text-success small pt-1 fw-bold">From</span> <span class="text-muted small pt-2 ps-1">Assessments System</span>
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
    const container = document.getElementById('students-with-ca-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const studentCount = document.getElementById('student-count');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.students-with-ca') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                studentCount.textContent = data.studentCount;
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching students with CA:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>

{{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
