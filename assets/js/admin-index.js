/**
 * Dashboard Chart Management
 * Handles chart creation and updates for the dashboard
 */
document.addEventListener('DOMContentLoaded', function() {
  // Check if required data is available
  if (typeof window.dashboardData === 'undefined') {
    console.error('Dashboard data not available');
    return;
  }

  const ctx = document.getElementById('dataChart');
  if (!ctx) {
    console.error('Chart canvas not found');
    return;
  }

  let chart = null;
  const chartContext = ctx.getContext('2d');
  
  // Chart configuration templates
  const chartConfigs = {
    transaction_sales: {
      title: 'Daily Total Revenue - Last 7 Days',
      data: window.dashboardData.transactionSalesData,
      label: 'Daily Sales (₱)',
      color: '#6777ef',
      backgroundColor: 'rgba(103, 119, 239, 0.1)',
      isCurrency: true
    },
    transaction_price: {
      title: 'Daily Medical Revenue - Last 7 Days',
      data: window.dashboardData.transactionSalesData,
      label: 'Transaction Price (₱)',
      color: '#fc544b',
      backgroundColor: 'rgba(252, 84, 75, 0.1)',
      isCurrency: true
    },
    orders_price: {
      title: 'Pet Shop Revenue - Last 7 Days',
      data: window.dashboardData.ordersPriceData,
      label: 'Orders Price (₱)',
      color: '#ffa426',
      backgroundColor: 'rgba(255, 164, 38, 0.1)',
      isCurrency: true
    }
  };

  /**
   * Create or update chart based on configuration
   * @param {string} configKey - The configuration key to use
   */
  function createChart(configKey) {
    const config = chartConfigs[configKey];
    
    if (!config) {
      console.error('Invalid chart configuration:', configKey);
      return;
    }
    
    // Show loading state
    const chartContainer = ctx.parentElement;
    chartContainer.classList.add('chart-loading');
    
    // Destroy existing chart if it exists
    if (chart) {
      chart.destroy();
    }

    // Update title
    const titleElement = document.getElementById('chartTitle');
    if (titleElement) {
      titleElement.textContent = config.title;
    }

    // Create new chart
    setTimeout(() => {
      chart = new Chart(chartContext, {
        type: 'line',
        data: {
          labels: window.dashboardData.labels,
          datasets: [{
            label: config.label,
            data: config.data,
            borderColor: config.color,
            backgroundColor: config.backgroundColor,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: config.color,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointHoverBorderWidth: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
          },
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12,
                  weight: '500'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: config.color,
              borderWidth: 1,
              cornerRadius: 8,
              displayColors: true,
              callbacks: {
                label: function(context) {
                  let value = context.parsed.y;
                  if (config.isCurrency) {
                    return config.label + ': ₱' + value.toLocaleString('en-US', {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2
                    });
                  } else {
                    return config.label + ': ' + value.toLocaleString();
                  }
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11
                },
                color: '#8a92b2'
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(138, 146, 178, 0.1)',
                borderDash: [5, 5]
              },
              ticks: {
                stepSize: undefined,
                maxTicksLimit: 8,
                font: {
                  size: 11
                },
                color: '#8a92b2',
                callback: function(value) {
                  if (config.isCurrency) {
                    return '₱' + value.toLocaleString('en-US', {
                      minimumFractionDigits: 0,
                      maximumFractionDigits: 0
                    });
                  } else {
                    return value.toLocaleString();
                  }
                }
              }
            }
          },
          elements: {
            point: {
              hoverRadius: 8,
              hoverBorderWidth: 3
            },
            line: {
              tension: 0.4
            }
          },
          interaction: {
            intersect: false,
            mode: 'index'
          }
        }
      });
      
      // Remove loading state
      chartContainer.classList.remove('chart-loading');
    }, 100);
  }

  /**
   * Handle chart type selection change
   */
  function handleChartTypeChange() {
    const selector = document.getElementById('chartTypeSelector');
    if (!selector) {
      console.error('Chart type selector not found');
      return;
    }

    selector.addEventListener('change', function() {
      const selectedType = this.value;
      if (chartConfigs[selectedType]) {
        createChart(selectedType);
      } else {
        console.error('Invalid chart type selected:', selectedType);
      }
    });
  }

  /**
   * Initialize dashboard charts
   */
  function initializeDashboard() {
    // Create initial chart
    createChart('transaction_sales');
    
    // Setup event listeners
    handleChartTypeChange();
    
    // Add resize handler for responsiveness
    window.addEventListener('resize', function() {
      if (chart) {
        chart.resize();
      }
    });
  }

  // Initialize the dashboard
  initializeDashboard();
  
  // Export functions for potential external use
  window.dashboardChart = {
    createChart: createChart,
    getChart: function() { return chart; },
    refreshChart: function() {
      const selector = document.getElementById('chartTypeSelector');
      if (selector) {
        createChart(selector.value);
      }
    }
  };
});