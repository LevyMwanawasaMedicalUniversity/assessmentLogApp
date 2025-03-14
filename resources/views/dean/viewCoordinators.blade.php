<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Coordinators</h1>
        @include('layouts.alerts')
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Coordinators</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @livewire('dashboard.coordinators-table', ['schoolId' => $schoolId ?? null, 'schoolName' => isset($results) && $results->count() > 0 ? $results->first()->SchoolName : null])
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

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
function coordinatorsStore() {
    return {
        data: [],
        isLoading: true,
        isRefreshing: false,
        hasError: false,
        lastUpdated: null,
        storageKey: 'viewCoordinators',
        schoolId: null,
        hasAdminPermission: {{ auth()->user()->hasPermissionTo('Administrator') ? 'true' : 'false' }},
        
        // Routes
        routes: {
            viewOnlyProgrammesWithCaForCoordinator: "{{ route('coordinator.viewOnlyProgrammesWithCaForCoordinator', ':id') }}",
            uploadFinalExamAndCa: "{{ route('pages.uploadFinalExamAndCa') }}",
            viewCoordinatorsCourses: "{{ route('admin.viewCoordinatorsCourses', ':id') }}"
        },
        
        init() {
            // Get schoolId from URL if present
            const urlParams = new URLSearchParams(window.location.search);
            this.schoolId = urlParams.get('schoolId');
            
            // Add schoolId to storage key if present to ensure different caches for different views
            if (this.schoolId) {
                this.storageKey = `viewCoordinators_${this.schoolId}`;
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
                var table = document.getElementById('myTable');
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

// Make sure Bootstrap Icons and XLSX are loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add Bootstrap Icons if not already included
    if (!document.querySelector('link[href*="bootstrap-icons"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css';
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
</x-app-layout>
