<div>
    <div class="card" x-data="coursesWithCaPerProgrammeStore()">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Courses With CA Per Programme</h4>
                <div>
                    <button type="button" class="btn btn-link text-secondary" @click="refreshData()" :disabled="isLoading">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-primary" @click="exportToCSV()" :disabled="!data.length">
                        Export to CSV
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body position-relative">
            <!-- Content sections with proper conditionals to avoid overlap -->
            
            <!-- 1. Loading State: ONLY show when no data is available -->
            <div x-show="isLoading && !data.length" x-cloak class="w-100 py-4 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <!-- 2. Error State: Only show when error and no fallback data -->
            <div x-show="hasError && !data.length" x-cloak class="alert alert-danger">
                Failed to load data. Please refresh the page.
            </div>
            
            <!-- 3. Data Display: Only show when we have data -->
            <div x-show="data.length > 0" x-cloak>
                <!-- Manual refresh indicator -->
                <div x-show="isRefreshing" x-cloak class="text-center mb-2">
                    <small class="text-muted">Refreshing data...</small>
                </div>
                
                <!-- Last updated timestamp -->
                <div class="text-right mb-2">
                    <small class="text-muted">
                        Last updated: <span x-text="formatLastUpdated(lastUpdated)"></span>
                    </small>
                </div>
                
                <!-- Chart container -->
                <div id="coursesWithCaPerProgrammeChart" style="min-height: 400px;" class="echart"></div>
            </div>
        </div>
    
        <style>
        .text-right {
            text-align: right;
        }
        [x-cloak] {
            display: none !important;
        }
        </style>
    
        <script>
        function coursesWithCaPerProgrammeStore() {
            return {
                data: [],
                isLoading: true,
                isRefreshing: false,
                hasError: false,
                lastUpdated: null,
                storageKey: 'coursesWithCaPerProgramme',
                
                init() {
                    // First check cache first before doing anything
                    const hasCachedData = this.loadFromCache();
                    
                    if (hasCachedData) {
                        // We have cached data - immediately turn off loading and render
                        this.isLoading = false;
                        
                        // Make sure the DOM is ready before rendering
                        this.$nextTick(() => {
                            this.renderChart();
                            
                            // Do a quiet refresh without showing any indicator
                            this.fetchDataQuietly();
                        });
                    } else {
                        // No cached data, so we need to show loading and fetch
                        this.fetchData(true);
                    }
                    
                    // Make chart responsive
                    window.addEventListener('resize', () => {
                        if (window.coursesWithCaChart) {
                            window.coursesWithCaChart.resize();
                        }
                    });
                },
                
                loadFromCache() {
                    try {
                        const cachedData = localStorage.getItem(this.storageKey);
                        if (cachedData) {
                            const parsed = JSON.parse(cachedData);
                            const parsedData = parsed.data || [];
                            
                            if (parsedData.length > 0) {
                                this.data = parsedData;
                                this.lastUpdated = parsed.timestamp || null;
                                return true; // We have valid cached data
                            }
                        }
                        return false; // No valid cached data
                    } catch (error) {
                        console.error('Error loading from cache:', error);
                        return false;
                    }
                },
                
                saveToCache(data) {
                    try {
                        const timestamp = new Date().toISOString();
                        localStorage.setItem(this.storageKey, JSON.stringify({
                            data: data,
                            timestamp: timestamp
                        }));
                        this.lastUpdated = timestamp;
                    } catch (error) {
                        console.error('Error saving to cache:', error);
                    }
                },
                
                // Main fetch - used for initial load and manual refreshes
                fetchData(showLoading = false) {
                    // Only show loading spinner if we have no data and specifically want loading
                    if (showLoading && !this.data.length) {
                        this.isLoading = true;
                    }
                    
                    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                // Extract the data - check both possible field names
                                const programmeData = data.coursesWithCaPerProgramme !== undefined 
                                    ? data.coursesWithCaPerProgramme 
                                    : (data.programmeData !== undefined ? data.programmeData : []);
                                
                                if (programmeData && programmeData.length > 0) {
                                    this.data = programmeData;
                                    this.saveToCache(programmeData);
                                    this.renderChart();
                                }
                            } else {
                                throw new Error('Data status is not success');
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching courses with CA per programme:', err);
                            if (!this.data.length) {
                                this.hasError = true;
                            }
                        })
                        .finally(() => {
                            this.isLoading = false;
                            this.isRefreshing = false;
                        });
                },
                
                // Quiet fetch - used for background refresh on init with cached data
                fetchDataQuietly() {
                    fetch('{{ route('api.dashboard.course-with-ca-per-programme') }}')
                        .then(response => response.ok ? response.json() : null)
                        .then(data => {
                            if (data && data.status === 'success') {
                                const programmeData = data.coursesWithCaPerProgramme !== undefined 
                                    ? data.coursesWithCaPerProgramme 
                                    : (data.programmeData !== undefined ? data.programmeData : []);
                                
                                if (programmeData && programmeData.length > 0) {
                                    this.data = programmeData;
                                    this.saveToCache(programmeData);
                                    this.renderChart();
                                }
                            }
                        })
                        .catch(err => console.error('Silent refresh error:', err));
                },
                
                // Manual refresh triggered by user
                refreshData() {
                    this.isRefreshing = true;
                    this.fetchData(false);
                },
                
                renderChart() {
                    if (!this.data.length) return;
                    
                    try {
                        // Prepare data for chart
                        const programmeNames = this.data.map(programme => programme.programme_name);
                        const coursesWithCA = this.data.map(programme => parseInt(programme.courses_with_ca) || 0);
                        const totalCourses = this.data.map(programme => parseInt(programme.total_courses) || 0);
                        
                        // Get chart container
                        const chartDom = document.getElementById('coursesWithCaPerProgrammeChart');
                        if (!chartDom) {
                            console.error('Chart DOM element not found');
                            return;
                        }
                        
                        // Clear previous chart instance if it exists
                        if (window.coursesWithCaChart) {
                            window.coursesWithCaChart.dispose();
                        }
                        
                        window.coursesWithCaChart = echarts.init(chartDom);
                        
                        // Chart options - ORIGINAL CHART DESIGN
                        const option = {
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: {
                                    type: 'shadow'
                                },
                                formatter: function(params) {
                                    // Get the programme name from the first series
                                    const programmeName = params[0].name;
                                    let tooltipContent = `<div style="font-weight:bold">${programmeName}</div>`;
                                    
                                    // Add each series data
                                    params.forEach(param => {
                                        const color = param.color;
                                        const seriesName = param.seriesName;
                                        const value = param.value;
                                        tooltipContent += `<div>
                                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:${color};margin-right:5px;"></span>
                                            <span>${seriesName}</span>: <span style="float:right;margin-left:20px;font-weight:bold">${value}</span>
                                        </div>`;
                                    });
                                    
                                    return tooltipContent;
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
                                },
                                axisLine: {
                                    lineStyle: {
                                        color: '#eee'
                                    }
                                }
                            },
                            yAxis: {
                                type: 'value',
                                splitLine: {
                                    lineStyle: {
                                        color: '#eee'
                                    }
                                }
                            },
                            series: [
                                {
                                    name: 'Courses with CA',
                                    type: 'bar',
                                    data: coursesWithCA,
                                    itemStyle: {
                                        color: '#4CAF50'  // Green
                                    }
                                },
                                {
                                    name: 'Total Courses',
                                    type: 'bar',
                                    data: totalCourses,
                                    itemStyle: {
                                        color: '#2196F3'  // Blue
                                    }
                                }
                            ]
                        };
                        
                        // Set the chart options
                        window.coursesWithCaChart.setOption(option);
                    } catch (error) {
                        console.error('Error creating chart:', error);
                        document.getElementById('coursesWithCaPerProgrammeChart').innerHTML = 
                            `<div class="text-center p-4 text-danger">Error creating chart: ${error.message}</div>`;
                    }
                },
                
                exportToCSV() {
                    if (!this.data || !this.data.length) {
                        alert('No data available to export');
                        return;
                    }
                    
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Programme,Courses with CA,Total Courses,Percentage\n";
                    
                    this.data.forEach(programme => {
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
                },
                
                formatLastUpdated(timestamp) {
                    if (!timestamp) return '';
                    
                    try {
                        const date = new Date(timestamp);
                        
                        // Check if date is valid
                        if (isNaN(date.getTime())) {
                            return 'Invalid date';
                        }
                        
                        // Format: "Today, 2:30 PM" or "Mar 15, 2:30 PM"
                        const now = new Date();
                        const isToday = date.toDateString() === now.toDateString();
                        
                        const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
                        const formattedTime = date.toLocaleTimeString(undefined, timeOptions);
                        
                        if (isToday) {
                            return `Today, ${formattedTime}`;
                        } else {
                            const dateOptions = { month: 'short', day: 'numeric' };
                            const formattedDate = date.toLocaleDateString(undefined, dateOptions);
                            return `${formattedDate}, ${formattedTime}`;
                        }
                    } catch (error) {
                        console.error('Error formatting date:', error);
                        return timestamp;
                    }
                }
            };
        }
    
        // Make sure ECharts and Font Awesome are loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Check and load ECharts if needed
            if (typeof echarts === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
                document.head.appendChild(script);
            }
            
            // Add Font Awesome if not already included
            if (!document.querySelector('link[href*="font-awesome"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
                document.head.appendChild(link);
            }
        });
        </script>
    </div>
</div>
