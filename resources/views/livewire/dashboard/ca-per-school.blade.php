<div class="card">
    <div class="card-body">
        <div class="justify-between d-flex justify-content-between">
            <h5 class="card-title">CA PER SCHOOL</h5>
            <button id="exportCSV" class="btn btn-primary mb-3 mt-3">Export to CSV</button>
        </div>

        <div id="ca-per-school-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <div id="verticalBarChart" style="min-height: 400px;" class="echart"></div>
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
    const container = document.getElementById('ca-per-school-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.ca-per-school') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.caPerSchool) {
                // Setup chart data
                const schoolNames = data.caPerSchool.map(item => item.school_name);
                const coursesWithCA = data.caPerSchool.map(item => item.courses_with_ca);
                const totalCourses = data.caPerSchool.map(item => item.total_courses);
                
                // Initialize the chart
                const verticalBarChart = echarts.init(document.getElementById('verticalBarChart'));
                
                // Configure chart options
                verticalBarChart.setOption({
                    title: {
                        text: ''
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {
                        data: ['Courses with CA', 'Total Courses']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: schoolNames
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, 0.01]
                    },
                    series: [
                        {
                            name: 'Courses with CA',
                            type: 'bar',
                            data: coursesWithCA
                        },
                        {
                            name: 'Total Courses',
                            type: 'bar',
                            data: totalCourses
                        }
                    ]
                });
                
                // Add resize listener
                window.addEventListener('resize', function() {
                    verticalBarChart.resize();
                });
                
                // Export to CSV functionality
                document.getElementById('exportCSV').addEventListener('click', function() {
                    // Prepare CSV data
                    let csvContent = "School,Courses with CA,Total Courses\n";
                    
                    for (let i = 0; i < schoolNames.length; i++) {
                        csvContent += `${schoolNames[i]},${coursesWithCA[i]},${totalCourses[i]}\n`;
                    }
                    
                    // Create download link
                    const encodedUri = encodeURI("data:text/csv;charset=utf-8," + csvContent);
                    const link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "ca_per_school.csv");
                    document.body.appendChild(link);
                    
                    // Download CSV file
                    link.click();
                });
                
                // Show content
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                throw new Error('Data is invalid or empty');
            }
        })
        .catch(err => {
            console.error('Error fetching CA per school data:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>
