<div>
    <div class="card" x-data="coordinatorsTableStore()">
        <div class="card-header card-header-primary">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Coordinators @isset($schoolId) in {{ $schoolName }} @else on Edurole @endif</h4>
                <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-light me-2" @click="refreshData()" :disabled="isLoading">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="btn btn-info btn-sm me-2" @click="exportToExcel()" :disabled="!data.length">
                        Export to Excel
                    </button>
                    @if (auth()->user()->hasPermissionTo('Administrator'))
                    <form method="POST" action="{{ route('admin.importCoordinators') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            Import Coordinators
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body position-relative">
            <!-- Loading State: ONLY show when no data is available -->
            <div x-show="isLoading && !data.length" x-cloak class="w-100 py-4 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading coordinators data...</p>
            </div>
            
            <!-- Error State: Only show when error and no fallback data -->
            <div x-show="hasError && !data.length" x-cloak class="alert alert-danger">
                Failed to load coordinators data. Please refresh the page.
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
                
                <!-- Table container -->
                <div style="overflow-x:auto;">
                    <table id="coordinatorsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Programme</th>
                                <th scope="col">School</th>
                                <th scope="col">Last Login</th>
                                <th scope="col">Courses</th>
                                <th scope="col">Courses With CA</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="data.length === 0">
                                <tr>
                                    <td colspan="9" class="text-center">No coordinators found</td>
                                </tr>
                            </template>
                            
                            <template x-for="(coordinator, index) in data" :key="index">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td x-text="coordinator.firstname"></td>
                                    <td x-text="coordinator.surname"></td>
                                    <td x-text="coordinator.name"></td>
                                    <td x-text="coordinator.school"></td>
                                    <td :style="{ color: coordinator.last_login !== 'NEVER' ? 'blue' : 'red' }" 
                                        x-text="coordinator.last_login"></td>
                                    <td x-text="coordinator.numberOfCourses + ' Courses'"></td>
                                    <td>
                                        <form :action="getViewProgrammesRoute(coordinator.id)" method="GET">
                                            <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;">
                                                <span x-text="coordinator.coursesWithCa + ' Courses'"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Button group">
                                            <template x-if="hasAdminPermission">
                                                <form method="GET" :action="getUploadFinalExamRoute()">
                                                    <input type="hidden" name="basicInformationId" :value="coordinator.encrypted_id">
                                                    <button type="submit" class="btn btn-success btn-sm me-1">
                                                        Final Exam
                                                    </button>
                                                </form>
                                            </template>
                                            <form method="GET" :action="getViewCoordinatorsCoursesRoute(coordinator.encrypted_id)">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Continuous Assessment
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
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
    function coordinatorsTableStore() {
        return {
            data: [],
            isLoading: true,
            isRefreshing: false,
            hasError: false,
            lastUpdated: null,
            storageKey: 'coordinatorsTable',
            schoolId: {{ $schoolId ?? 'null' }},
            hasAdminPermission: {{ auth()->user()->hasPermissionTo('Administrator') ? 'true' : 'false' }},
            
            // Routes
            routes: {
                viewOnlyProgrammesWithCaForCoordinator: "{{ route('coordinator.viewOnlyProgrammesWithCaForCoordinator', ':id') }}",
                uploadFinalExamAndCa: "{{ route('pages.uploadFinalExamAndCa') }}",
                viewCoordinatorsCourses: "{{ route('admin.viewCoordinatorsCourses', ':id') }}"
            },
            
            init() {
                // Add schoolId to storage key if present to ensure different caches for different views
                if (this.schoolId) {
                    this.storageKey = `coordinatorsTable_${this.schoolId}`;
                }
                
                // First check cache before doing anything
                const hasCachedData = this.loadFromCache();
                
                if (hasCachedData) {
                    // We have cached data - immediately turn off loading and render
                    this.isLoading = false;
                    
                    // Do a quiet refresh without showing any indicator
                    this.fetchDataQuietly();
                } else {
                    // No cached data, so we need to show loading and fetch
                    this.fetchData(true);
                }
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
                
                const apiUrl = `{{ route('api.coordinators.data') }}${this.schoolId ? '?schoolId=' + this.schoolId : ''}`;
                
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Extract the data
                            const coordinatorsData = data.coordinators || [];
                            
                            // Ensure coordinators is an array
                            const coordinatorsArray = Array.isArray(coordinatorsData) 
                                ? coordinatorsData 
                                : Object.values(coordinatorsData);
                            
                            if (coordinatorsArray && coordinatorsArray.length > 0) {
                                this.data = coordinatorsArray;
                                this.saveToCache(coordinatorsArray);
                            } else {
                                // Empty data
                                this.data = [];
                                this.saveToCache([]);
                            }
                        } else {
                            throw new Error('Data status is not success');
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching coordinators data:', err);
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
                
                const apiUrl = `{{ route('api.coordinators.data') }}${this.schoolId ? '?schoolId=' + this.schoolId : ''}`;
                
                fetch(apiUrl)
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (data && data.status === 'success') {
                            // Extract the data
                            const coordinatorsData = data.coordinators || [];
                            
                            // Ensure coordinators is an array
                            const coordinatorsArray = Array.isArray(coordinatorsData) 
                                ? coordinatorsData 
                                : Object.values(coordinatorsData);
                            
                            if (coordinatorsArray && coordinatorsArray.length > 0) {
                                this.data = coordinatorsArray;
                                this.saveToCache(coordinatorsArray);
                            } else {
                                // Empty data
                                this.data = [];
                                this.saveToCache([]);
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
            
            // Helper method to get route URLs with proper IDs
            getViewProgrammesRoute(id) {
                return this.routes.viewOnlyProgrammesWithCaForCoordinator.replace(':id', id);
            },
            
            getViewCoordinatorsCoursesRoute(id) {
                return this.routes.viewCoordinatorsCourses.replace(':id', id);
            },
            
            getUploadFinalExamRoute() {
                return this.routes.uploadFinalExamAndCa;
            },
            
            // Export functionality
            exportToExcel() {
                if (!this.data || !this.data.length) {
                    alert('No data available to export');
                    return;
                }
                
                try {
                    var table = document.getElementById('coordinatorsTable');
                    var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
                    XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
                } catch (error) {
                    console.error('Error exporting to Excel:', error);
                    alert('Failed to export data. Please make sure the XLSX library is loaded.');
                }
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

    // Make sure external libraries are loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Font Awesome is loaded, if not load it
        if (!document.querySelector('link[href*="font-awesome"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
            document.head.appendChild(link);
        }
        
        // Check if XLSX is loaded, if not load it
        if (typeof XLSX === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
            document.head.appendChild(script);
        }
    });
    </script>
</div>
