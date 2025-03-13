<div class="card">
    <div class="card-body pb-0">
        <h5 class="card-title">Number Of Coordinators <span class="coordinator-count-text"></span></h5>

        <div id="coordinators-traffic-container">
            <div class="loading-spinner d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="content-container d-none">
                <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
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
    const container = document.getElementById('coordinators-traffic-container');
    const spinner = container.querySelector('.loading-spinner');
    const content = container.querySelector('.content-container');
    const error = container.querySelector('.error-container');
    const coordinatorCountText = document.querySelector('.coordinator-count-text');
    
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
                // Display total coordinator count
                coordinatorCountText.textContent = `: ${data.coordinatorsCount}`;
                
                // Prepare chart data
                const chartData = data.schoolNames.map((school, index) => ({
                    value: data.userCounts[index],
                    name: school
                }));
                
                // Initialize the chart
                const trafficChart = echarts.init(document.getElementById('trafficChart'));
                
                // Configure chart options
                trafficChart.setOption({
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        top: '5%',
                        left: 'center'
                    },
                    series: [{
                        name: 'Users Per School',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '18',
                                fontWeight: 'bold'
                            }
                        },
                        labelLine: {
                            show: false
                        },
                        data: chartData
                    }]
                });
                
                // Add resize listener
                window.addEventListener('resize', function() {
                    trafficChart.resize();
                });
                
                // Show content
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching coordinators traffic data:', err);
            spinner.classList.add('d-none');
            error.classList.remove('d-none');
        });
});
</script>
