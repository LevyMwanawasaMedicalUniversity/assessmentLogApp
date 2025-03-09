import './bootstrap';

// Import Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import ApexCharts
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

// Import Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import Alpine.js
import Alpine from 'alpinejs';

// Initialize Alpine.js if available
try {
    window.Alpine = Alpine;
    Alpine.start();
} catch (e) {
    console.log('Alpine.js not loaded');
}

// Add CSRF token to all axios requests
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Main JS for sidebar toggle and other UI interactions
document.addEventListener('DOMContentLoaded', function() {
    // Toggle the sidebar
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar-btn');
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', () => {
            document.querySelector('body').classList.toggle('toggle-sidebar');
        });
    }

    // Toggle the search bar on mobile
    const searchBarToggle = document.querySelector('.search-bar-toggle');
    if (searchBarToggle) {
        searchBarToggle.addEventListener('click', () => {
            document.querySelector('.search-bar').classList.toggle('search-bar-show');
        });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
