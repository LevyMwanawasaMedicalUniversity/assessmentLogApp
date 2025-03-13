<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">CA PER SCHOOL</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshCaPerSchool()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportCaPerSchoolToCSV()">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <!-- Horizontal Bar Chart -->
            <div id="caPerSchoolChart" style="min-height: 400px;" class="echart mb-4"></div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>

<script>
// Function to fetch CA per school data
document.addEventListener('DOMContentLoaded', function() {
    fetchCaPerSchool();
});

function fetchCaPerSchool() {
    const error = document.querySelector('.error-container');
    const chartContainer = document.getElementById('caPerSchoolChart');
    
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
    fetch('{{ route('api.dashboard.ca-per-school') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('CA Per School API Response:', data);
            if (data.status === 'success') {
                if (data.caPerSchool && data.caPerSchool.length > 0) {
                    // Prepare data for chart
                    const schoolNames = data.caPerSchool.map(school => school.school_name);
                    const coursesWithCA = data.caPerSchool.map(school => school.courses_with_ca);
                    const totalCourses = data.caPerSchool.map(school => school.total_courses);
                    
                    // Create the horizontal bar chart
                    createHorizontalBarChart(schoolNames, coursesWithCA, totalCourses);
                } else {
                    // No data found
                    chartContainer.innerHTML = `<div class="text-center p-4">No CA per school data found</div>`;
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching CA per school:', err);
            chartContainer.innerHTML = `<div class="text-center p-4 text-danger">Failed to load chart data</div>`;
            error.classList.remove('d-none');
        });
}

function createHorizontalBarChart(schoolNames, coursesWithCA, totalCourses) {
    // Define colors for the chart (matching Material Dashboard UI color scheme)
    const colors = ['#4CAF50', '#2196F3']; // Green for Courses with CA, Blue for Total Courses
    
    // Initialize the chart
    const chart = echarts.init(document.getElementById('caPerSchoolChart'));
    
    // Chart options
    const option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['Courses with CA', 'Total Courses'],
            textStyle: {
                color: '#333'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            boundaryGap: [0, 0.01]
        },
        yAxis: {
            type: 'category',
            data: schoolNames
        },
        series: [
            {
                name: 'Courses with CA',
                type: 'bar',
                data: coursesWithCA,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: 'Total Courses',
                type: 'bar',
                data: totalCourses,
                itemStyle: {
                    color: colors[1]
                }
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

function exportCaPerSchoolToCSV() {
    fetch('{{ route('api.dashboard.ca-per-school') }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.caPerSchool && data.caPerSchool.length > 0) {
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "School,Courses with CA,Total Courses,Percentage\n";
                
                data.caPerSchool.forEach(school => {
                    const percentage = school.total_courses > 0 
                        ? ((school.courses_with_ca / school.total_courses) * 100).toFixed(2) 
                        : '0.00';
                    
                    csvContent += `${school.school_name || 'N/A'},${school.courses_with_ca || 0},${school.total_courses || 0},${percentage}%\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "CA_per_School.csv");
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
function refreshCaPerSchool() {
    fetchCaPerSchool();
}
</script>

<!-- Include ECharts library if not already included -->
<script>
    if (typeof echarts === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
        script.onload = function() {
            fetchCaPerSchool();
        };
        document.head.appendChild(script);
    }
</script>
