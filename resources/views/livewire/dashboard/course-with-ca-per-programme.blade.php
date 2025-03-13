<div class="card">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Courses With CA Per Programme</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshCoursesWithCaPerProgramme()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportCoursesWithCaPerProgrammeToCSV()">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="content-container">
            <!-- Vertical Bar Chart -->
            <div id="coursesWithCaPerProgrammeChart" style="min-height: 400px;" class="echart mb-4"></div>
        </div>
        
        <div class="error-container alert alert-danger d-none">
            Failed to load data. Please refresh the page.
        </div>
    </div>
</div>

<script>
// Function to fetch courses with CA per programme data
document.addEventListener('DOMContentLoaded', function() {
    // Check if ECharts is already loaded
    if (typeof echarts !== 'undefined') {
        fetchCoursesWithCaPerProgramme();
    } else {
        // Load ECharts first
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
        script.onload = function() {
            fetchCoursesWithCaPerProgramme();
        };
        document.head.appendChild(script);
    }
});

function fetchCoursesWithCaPerProgramme() {
    const error = document.querySelector('.error-container');
    const chartContainer = document.getElementById('coursesWithCaPerProgrammeChart');
    
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
    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Courses With CA Per Programme API Response:', data);
            if (data.status === 'success') {
                // Check both possible field names in the API response
                const programmeData = data.coursesWithCaPerProgramme !== undefined 
                    ? data.coursesWithCaPerProgramme 
                    : (data.programmeData !== undefined ? data.programmeData : []);
                
                if (programmeData && programmeData.length > 0) {
                    // Clear the chart container before creating a new chart
                    chartContainer.innerHTML = '';
                    
                    // Prepare data for chart
                    const programmeNames = programmeData.map(programme => programme.programme_name);
                    const coursesWithCA = programmeData.map(programme => parseInt(programme.courses_with_ca) || 0);
                    const totalCourses = programmeData.map(programme => parseInt(programme.total_courses) || 0);
                    
                    // Create the vertical bar chart
                    createVerticalBarChart(programmeNames, coursesWithCA, totalCourses);
                } else {
                    // No data found
                    chartContainer.innerHTML = `<div class="text-center p-4">No programme data found</div>`;
                }
            } else {
                throw new Error('Data status is not success');
            }
        })
        .catch(err => {
            console.error('Error fetching courses with CA per programme:', err);
            chartContainer.innerHTML = `<div class="text-center p-4 text-danger">Failed to load chart data</div>`;
            error.classList.remove('d-none');
        });
}

function createVerticalBarChart(programmeNames, coursesWithCA, totalCourses) {
    // Define colors for the chart (matching Material Dashboard UI color scheme)
    const colors = ['#4CAF50', '#2196F3']; // Green for Courses with CA, Blue for Total Courses
    
    try {
        // Initialize the chart
        const chartDom = document.getElementById('coursesWithCaPerProgrammeChart');
        const chart = echarts.init(chartDom);
        
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
                type: 'category',
                data: programmeNames,
                axisLabel: {
                    interval: 0,
                    rotate: 45,
                    textStyle: {
                        fontSize: 10
                    }
                }
            },
            yAxis: {
                type: 'value'
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
    } catch (error) {
        console.error('Error creating chart:', error);
        document.getElementById('coursesWithCaPerProgrammeChart').innerHTML = 
            `<div class="text-center p-4 text-danger">Error creating chart: ${error.message}</div>`;
    }
}

function exportCoursesWithCaPerProgrammeToCSV() {
    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
        .then(response => response.json())
        .then(data => {
            // Check both possible field names in the API response
            const programmeData = data.coursesWithCaPerProgramme !== undefined 
                ? data.coursesWithCaPerProgramme 
                : (data.programmeData !== undefined ? data.programmeData : []);
            
            if (data.status === 'success' && programmeData && programmeData.length > 0) {
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "Programme,Courses with CA,Total Courses,Percentage\n";
                
                programmeData.forEach(programme => {
                    const percentage = programme.total_courses > 0 
                        ? ((programme.courses_with_ca / programme.total_courses) * 100).toFixed(2) 
                        : '0.00';
                    
                    csvContent += `${programme.programme_name || 'N/A'},${programme.courses_with_ca || 0},${programme.total_courses || 0},${percentage}%\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "Courses_With_CA_Per_Programme.csv");
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
function refreshCoursesWithCaPerProgramme() {
    fetchCoursesWithCaPerProgramme();
}
</script>
