<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Coordinators Per School</h4>
            <button type="button" class="btn btn-sm btn-light" onclick="refreshCoordinatorsTraffic()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <div class="d-flex align-items-center mb-4">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                    <h6 id="total-coordinators">0</h6>
                    <span class="text-primary small pt-1 fw-bold">Total Coordinators</span>
                </div>
            </div>
            
            <!-- Donut Chart -->
            <div id="coordinatorsDonutChart" style="min-height: 300px;" class="echart"></div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>

<script>
// Function to fetch coordinators traffic data
document.addEventListener('DOMContentLoaded', function() {
    fetchCoordinatorsTraffic();
});

function fetchCoordinatorsTraffic() {
    const error = document.querySelector('.error-container');
    const totalCoordinators = document.getElementById('total-coordinators');
    const chartContainer = document.getElementById('coordinatorsDonutChart');
    
    // Show loading in chart container
    chartContainer.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    totalCoordinators.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Loading...</span>
        </div>
    `;
    
    error.classList.add('d-none');
    
    // Fetch data from API
    fetch('{{ route('api.dashboard.coordinators-traffic') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Coordinators Traffic API Response:', data);
            if (data.status === 'success') {
                // Update total coordinators count
                const formattedTotal = new Intl.NumberFormat().format(data.totalCoordinators || 0);
                totalCoordinators.textContent = formattedTotal;
                
                if (data.coordinatorsPerSchool && data.coordinatorsPerSchool.length > 0) {
                    // Prepare data for chart
                    const chartData = data.coordinatorsPerSchool.map(school => ({
                        value: school.coordinator_count,
                        name: school.school_name
                    }));
                    
                    // Create the donut chart
                    createDonutChart(chartData);
                } else {
                    // No data found
                    chartContainer.innerHTML = `<div class="text-center p-4">No coordinator data found</div>`;
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching coordinators traffic:', err);
            totalCoordinators.textContent = '0';
            chartContainer.innerHTML = `<div class="text-center p-4 text-danger">Failed to load chart data</div>`;
            error.classList.remove('d-none');
        });
}

function createDonutChart(data) {
    // Define colors for the chart (matching Material Dashboard UI color scheme)
    const colors = [
        '#4CAF50', // Green
        '#2196F3', // Blue
        '#FFC107', // Amber
        '#F44336', // Red
        '#9C27B0', // Purple
        '#00BCD4'  // Cyan
    ];
    
    // Initialize the chart
    const chart = echarts.init(document.getElementById('coordinatorsDonutChart'));
    
    // Chart options
    const option = {
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            top: '5%',
            left: 'center',
            textStyle: {
                color: '#333'
            }
        },
        color: colors,
        series: [
            {
                name: 'Coordinators',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
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
                data: data
            }
        ]
    };
    
    // Set the chart options
    chart.setOption(option);
    
    // Make chart responsive
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Function to refresh data
function refreshCoordinatorsTraffic() {
    fetchCoordinatorsTraffic();
}
</script>

<!-- Include ECharts library if not already included -->
<script>
    if (typeof echarts === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
        script.onload = function() {
            fetchCoordinatorsTraffic();
        };
        document.head.appendChild(script);
    }
</script>
