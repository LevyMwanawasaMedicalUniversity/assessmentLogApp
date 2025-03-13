<div class="card">
    <div class="card-body">
        <h5 class="card-title">Deans Per School</h5>

        <div id="deans-per-school-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">School</th>
                            <th scope="col">Dean</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="deans-table-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
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
    const container = document.getElementById('deans-per-school-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const tableBody = document.getElementById('deans-table-body');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.deans-per-school') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                if (data.deans && data.deans.length > 0) {
                    let html = '';
                    data.deans.forEach((dean, index) => {
                        html += `
                            <tr>
                                <th scope="row">${index + 1}</th>
                                <td>${dean.school}</td>
                                <td>${dean.dean}</td>
                                <td>${dean.email}</td>
                                <td>
                                    <a href="{{ route('admin.viewCoordinatorsUnderDean', ['schoolId' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', btoa(dean.parentId))>
                                        <span class="badge bg-success">View</span>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    tableBody.innerHTML = html;
                    spinner.classList.add('d-none');
                    content.classList.remove('d-none');
                } else {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No deans found</td></tr>';
                    spinner.classList.add('d-none');
                    content.classList.remove('d-none');
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching deans per school:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>
