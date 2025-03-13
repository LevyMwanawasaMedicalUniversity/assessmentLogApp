<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h5 class="card-title">Top Programmes <span>| With CA</span></h5>
            <button id="exportProgrammesCSV" class="btn btn-primary mb-3 mt-3">Export to CSV</button>
        </div>

        <div id="course-with-ca-per-programme-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <div id="programmeBarChart" style="min-height: 400px;" class="echart"></div>
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
    const container = document.getElementById('course-with-ca-per-programme-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.courseWithCaPerProgramme) {
                // Setup chart data
                const programmes = data.courseWithCaPerProgramme.map(item => item.programme_name);
                const assessmentCounts = data.courseWithCaPerProgramme.map(item => item.assessment_count);
                
                // Initialize the chart
                const programmeBarChart = echarts.init(document.getElementById('programmeBarChart'));
                
                // Configure chart options
                programmeBarChart.setOption({
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: programmes,
                        axisLabel: {
                            rotate: 45,
                            interval: 0
                        }
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: 'Assessments',
                            type: 'bar',
                            data: assessmentCounts,
                            itemStyle: {
                                color: '#4e73df'
                            }
                        }
                    ]
                });
                
                // Add resize listener
                window.addEventListener('resize', function() {
                    programmeBarChart.resize();
                });
                
                // Export to CSV functionality
                document.getElementById('exportProgrammesCSV').addEventListener('click', function() {
                    // Prepare CSV data
                    let csvContent = "Programme,Assessment Count\n";
                    
                    for (let i = 0; i < programmes.length; i++) {
                        csvContent += `"${programmes[i]}",${assessmentCounts[i]}\n`;
                    }
                    
                    // Create download link
                    const encodedUri = encodeURI("data:text/csv;charset=utf-8," + csvContent);
                    const link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "programmes_with_ca.csv");
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
            console.error('Error fetching programme data:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>
