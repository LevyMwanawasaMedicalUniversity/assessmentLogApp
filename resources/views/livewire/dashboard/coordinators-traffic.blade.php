<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">COORDINATORS TRAFFIC</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshCoordinatorsTraffic()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportCoordinatorsTrafficToCSV()">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <!-- Donut Chart -->
            <div id="coordinatorsTrafficChart" style="min-height: 400px;" class="echart mb-4"></div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>

<script>
// Function to fetch coordinators traffic data
document.addEventListener('DOMContentLoaded', function() {
    // Check if ECharts is already loaded
    if (typeof echarts !== 'undefined') {
        fetchCoordinatorsTraffic();
    } else {
        // Load ECharts first
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
        script.onload = function() {
            fetchCoordinatorsTraffic();
        };
        document.head.appendChild(script);
    }
});

function fetchCoordinatorsTraffic() {
    const error = document.querySelector('.error-container');
    const chartContainer = document.getElementById('coordinatorsTrafficChart');
    
    // Show loading in chart container
    chartContainer.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
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
                // Use coordinatorsPerSchool from API response (matching the backend)
                const coordinatorsData = data.coordinatorsPerSchool || [];
                
                if (coordinatorsData && coordinatorsData.length > 0) {
                    // Clear the chart container before creating a new chart
                    chartContainer.innerHTML = '';
                    
                    // Calculate total coordinators from the data or use the provided total
                    const totalCoordinators = data.totalCoordinators || coordinatorsData.reduce((total, item) => {
                        return total + parseInt(item.coordinator_count || 0);
                    }, 0);
                    
                    // Create the donut chart
                    createDonutChart(coordinatorsData, totalCoordinators);
                } else {
                    // No data found
                    chartContainer.innerHTML = `<div class="text-center p-4">No coordinators traffic data found</div>`;
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching coordinators traffic:', err);
            chartContainer.innerHTML = `<div class="text-center p-4 text-danger">Failed to load chart data</div>`;
            error.classList.remove('d-none');
        });
}

function createDonutChart(coordinatorsData, totalCoordinators) {
    try {
        // Initialize the chart
        const chartDom = document.getElementById('coordinatorsTrafficChart');
        const chart = echarts.init(chartDom);
        
        // Prepare data for chart
        const schoolNames = coordinatorsData.map(item => item.school_name);
        const coordinatorCounts = coordinatorsData.map(item => parseInt(item.coordinator_count) || 0);
        
        // Generate colors for the chart
        const colors = [
            '#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0', 
            '#3F51B5', '#E91E63', '#009688', '#795548', '#607D8B'
        ];
        
        // Chart options
        const option = {
            title: {
                text: `Total Coordinators: ${totalCoordinators}`,
                left: 'center',
                top: '0%',
                textStyle: {
                    color: '#333',
                    fontSize: 16,
                    fontWeight: 'bold'
                }
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                top: 'middle',
                data: schoolNames,
                textStyle: {
                    color: '#333'
                }
            },
            series: [
                {
                    name: 'Coordinators',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['65%', '50%'],
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
                    data: schoolNames.map((name, index) => {
                        return {
                            value: coordinatorCounts[index],
                            name: name,
                            itemStyle: {
                                color: colors[index % colors.length]
                            }
                        };
                    })
                }
            ]
        };
        
        // Set the chart options
        chart.setOption(option);
        
        // Make chart responsive
        window.addEventListener('resize', function() {
            chart.resize();
        });
    } catch (error) {
        console.error('Error creating chart:', error);
        document.getElementById('coordinatorsTrafficChart').innerHTML = 
            `<div class="text-center p-4 text-danger">Error creating chart: ${error.message}</div>`;
    }
}

function exportCoordinatorsTrafficToCSV() {
    fetch('{{ route('api.dashboard.coordinators-traffic') }}')
        .then(response => response.json())
        .then(data => {
            // Use coordinatorsPerSchool from API response (matching the backend)
            const coordinatorsData = data.coordinatorsPerSchool || [];
            
            if (data.status === 'success' && coordinatorsData && coordinatorsData.length > 0) {
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "School,Coordinator Count\n";
                
                coordinatorsData.forEach(item => {
                    csvContent += `${item.school_name || 'N/A'},${item.coordinator_count || 0}\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "Coordinators_Traffic.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('No data available to export');
            }
        })
        .catch(err => {
            console.error('Error exporting data:', err);
            alert('Failed to export data. Please try again.');
        });
}

// Function to refresh data
function refreshCoordinatorsTraffic() {
    fetchCoordinatorsTraffic();
}
</script>
