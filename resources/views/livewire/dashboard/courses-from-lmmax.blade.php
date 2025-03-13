<div class="card info-card customers-card">                    
    <div class="card-body">
        <h5 class="card-title">Courses From LM-MAX <span>| Total</span></h5>
        
        <div id="courses-from-lmmax-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                @if (auth()->user()->hasPermissionTo('Registrar'))
                    <a href="{{ route('coordinator.viewOnlyProgrammesWithCa') }}">
                @endif
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-courses-with-ca">0</h6>
                            <span class="text-success small pt-1 fw-bold">With Continuous Assessments</span>
                        </div>
                    </div>
                @if (auth()->user()->hasPermissionTo('Registrar'))
                    </a>
                @endif
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
    const container = document.getElementById('courses-from-lmmax-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const totalCoursesWithCa = document.getElementById('total-courses-with-ca');
    
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
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses from LMMAX:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>

{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
