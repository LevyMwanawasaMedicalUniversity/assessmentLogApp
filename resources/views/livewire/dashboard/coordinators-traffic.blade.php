<div class="card" x-data="coordinatorsTrafficStore()">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">COORDINATORS TRAFFIC</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" @click="refreshData()" :disabled="isLoading">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" @click="exportToCSV()" :disabled="!data.length">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
    <div class="card-body position-relative">
        <!-- Loading State: ONLY show when no data is available -->
        <div x-show="isLoading && !data.length" x-cloak class="w-100 py-4 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <!-- Error State: Only show when error and no fallback data -->
        <div x-show="hasError && !data.length" x-cloak class="alert alert-danger">
            Failed to load data. Please refresh the page.
        </div>
        
        <!-- Data Display: Only show when we have data -->
        <div x-show="data.length > 0" x-cloak>
            <!-- Manual refresh indicator with animation -->
            <div x-show="isRefreshing" x-cloak class="text-center mb-2 refreshing-text">
                <small class="text-muted">Refreshing data...</small>
            </div>
            
            <!-- Last updated timestamp -->
            <div class="text-right mb-2">
                <small class="text-muted">
                    Last updated: <span x-text="formatLastUpdated(lastUpdated)"></span>
                </small>
            </div>
            
            <!-- Chart container with minimal height -->
            <div id="coordinatorsTrafficChart" style="height: 400px;" class="echart mb-4"></div>
        </div>
    </div>

    <style>
    .text-right {
        text-align: right;
    }
    [x-cloak] {
        display: none !important;
    }
    .refreshing-text {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.5; }
        50% { opacity: 1; }
        100% { opacity: 0.5; }
    }
    </style>

    <script>
    function coordinatorsTrafficStore() {
        return {
            data: [],
            totalCoordinators: 0,
            isLoading: true,
            isRefreshing: false,
            hasError: false,
            lastUpdated: null,
            storageKey: 'coordinatorsTraffic',
            
            init() {
                // First check cache before doing anything
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
                    if (window.coordinatorsChart && typeof window.coordinatorsChart.resize === 'function') {
                        window.coordinatorsChart.resize();
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
                            this.totalCoordinators = parsed.totalCoordinators || this.calculateTotal(parsedData);
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
            
            calculateTotal(data) {
                return data.reduce((total, item) => {
                    return total + parseInt(item.coordinator_count || 0);
                }, 0);
            },
            
            saveToCache(data, total) {
                try {
                    const timestamp = new Date().toISOString();
                    localStorage.setItem(this.storageKey, JSON.stringify({
                        data: data,
                        totalCoordinators: total,
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
                
                fetch('{{ route('api.dashboard.coordinators-traffic') }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Extract the data
                            const coordinatorsData = data.coordinatorsPerSchool || [];
                            const totalCoordinators = data.totalCoordinators || this.calculateTotal(coordinatorsData);
                            
                            if (coordinatorsData && coordinatorsData.length > 0) {
                                this.data = coordinatorsData;
                                this.totalCoordinators = totalCoordinators;
                                this.saveToCache(coordinatorsData, totalCoordinators);
                                this.renderChart();
                            }
                        } else {
                            throw new Error('Data status is not success');
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching coordinators traffic:', err);
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
                // Show refreshing indicator when fetching data
                this.isRefreshing = true;
                
                fetch('{{ route('api.dashboard.coordinators-traffic') }}')
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (data && data.status === 'success') {
                            const coordinatorsData = data.coordinatorsPerSchool || [];
                            const totalCoordinators = data.totalCoordinators || this.calculateTotal(coordinatorsData);
                            
                            if (coordinatorsData && coordinatorsData.length > 0) {
                                this.data = coordinatorsData;
                                this.totalCoordinators = totalCoordinators;
                                this.saveToCache(coordinatorsData, totalCoordinators);
                                this.renderChart();
                            }
                        }
                    })
                    .catch(err => console.error('Silent refresh error:', err))
                    .finally(() => {
                        this.isRefreshing = false;
                    });
            },
            
            // Manual refresh triggered by user
            refreshData() {
                this.isRefreshing = true;
                this.fetchData(false);
            },
            
            renderChart() {
                if (!this.data.length) return;
                
                try {
                    // Check if ECharts is available
                    if (typeof echarts === 'undefined') {
                        console.error('ECharts library is not loaded');
                        return;
                    }
                    
                    // Get chart container
                    const chartDom = document.getElementById('coordinatorsTrafficChart');
                    if (!chartDom) {
                        console.error('Chart DOM element not found');
                        return;
                    }
                    
                    // Generate colors for the chart
                    const colors = [
                        '#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0', 
                        '#3F51B5', '#E91E63', '#009688', '#795548', '#607D8B'
                    ];
                    
                    // Prepare data for chart
                    const schoolNames = this.data.map(item => item.school_name);
                    const coordinatorCounts = this.data.map(item => parseInt(item.coordinator_count) || 0);
                    
                    // Clear any previous chart instance
                    if (window.coordinatorsChart && typeof window.coordinatorsChart.dispose === 'function') {
                        window.coordinatorsChart.dispose();
                    }
                    
                    // Initialize chart
                    window.coordinatorsChart = echarts.init(chartDom);
                    
                    // Chart options for donut chart
                    const option = {
                        title: {
                            text: `Total Coordinators: ${this.totalCoordinators}`,
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
                    
                    // Apply the options
                    window.coordinatorsChart.setOption(option);
                } catch (error) {
                    console.error('Error creating chart:', error);
                    document.getElementById('coordinatorsTrafficChart').innerHTML = 
                        `<div class="text-center p-4 text-danger">Error creating chart: ${error.message}</div>`;
                }
            },
            
            exportToCSV() {
                if (!this.data || !this.data.length) {
                    alert('No data available to export');
                    return;
                }
                
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "School,Coordinator Count\n";
                
                this.data.forEach(item => {
                    csvContent += `${item.school_name || 'N/A'},${item.coordinator_count || 0}\n`;
                });
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "Coordinators_Traffic.csv");
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

    // Make sure ECharts and Bootstrap Icons are loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check and load ECharts if needed
        if (typeof echarts === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js';
            document.head.appendChild(script);
        }
        
        // Add Bootstrap Icons if not already included
        if (!document.querySelector('link[href*="bootstrap-icons"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css';
            document.head.appendChild(link);
        }
    });
    </script>
</div>