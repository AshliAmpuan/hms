/**
 * Google Analytics Configuration
 * Handles Google Analytics tracking setup
 */

// Initialize Google Analytics
window.dataLayer = window.dataLayer || [];

function gtag() {
  dataLayer.push(arguments);
}

// Configure Google Analytics
gtag('js', new Date());
gtag('config', 'UA-94034622-3');

// Optional: Add custom tracking events for dashboard interactions
function trackDashboardEvent(action, category = 'Dashboard', label = '') {
  if (typeof gtag === 'function') {
    gtag('event', action, {
      event_category: category,
      event_label: label,
      value: 1
    });
  }
}

// Track chart type changes
document.addEventListener('DOMContentLoaded', function() {
  const chartSelector = document.getElementById('chartTypeSelector');
  if (chartSelector) {
    chartSelector.addEventListener('change', function() {
      trackDashboardEvent('chart_type_changed', 'Dashboard', this.value);
    });
  }
});

// Export tracking function for use in other scripts
window.trackDashboardEvent = trackDashboardEvent;