<div class="card" x-data="deansPerSchoolStore()">
    <div class="card-header card-header-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Deans Per School</h4>
            <div>
                <button type="button" class="btn btn-sm btn-light" @click="refreshData()" :disabled="isLoading">
                    <i class="bi bi-arrow-clockwise"></i>
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
            
            <!-- Table container -->
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>School</th>
                            <th>Dean</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="data.length === 0">
                            <tr>
                                <td colspan="3" class="text-center">No deans found</td>
                            </tr>
                        </template>
                        
                        <template x-for="(dean, index) in data" :key="index">
                            <tr>
                                <td x-text="getSchoolName(dean)"></td>
                                <td x-text="getDeanName(dean)"></td>
                                <td x-text="getEmail(dean)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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
    function deansPerSchoolStore() {
        return {
            data: [],
            isLoading: true,
            isRefreshing: false,
            hasError: false,
            lastUpdated: null,
            storageKey: 'deansPerSchool',
            
            init() {
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
                
                fetch('{{ route('api.dashboard.deans-per-school') }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Extract the data
                            const deansData = data.deans || [];
                            
                            if (deansData && deansData.length > 0) {
                                this.data = deansData;
                                this.saveToCache(deansData);
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
                        console.error('Error fetching deans per school:', err);
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
                
                fetch('{{ route('api.dashboard.deans-per-school') }}')
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (data && data.status === 'success') {
                            const deansData = data.deans || [];
                            
                            if (deansData && deansData.length > 0) {
                                this.data = deansData;
                                this.saveToCache(deansData);
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
            
            // Helper methods to handle different API response formats
            getSchoolName(dean) {
                return dean.Description || dean.school_name || dean.school || 'N/A';
            },
            
            getDeanName(dean) {
                if (dean.FirstName && dean.Surname) {
                    return `${dean.FirstName} ${dean.Surname}`;
                }
                return dean.dean_name || dean.dean || 'N/A';
            },
            
            getEmail(dean) {
                return dean.PrivateEmail || dean.email || 'N/A';
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

    // Make sure Bootstrap Icons are loaded
    document.addEventListener('DOMContentLoaded', function() {
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