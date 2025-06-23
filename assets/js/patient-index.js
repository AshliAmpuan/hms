/**
 * Dashboard JavaScript functionality
 * Handles pet registration modal and related interactions
 */

$(document).ready(function() {
    // Initialize dashboard functionality
    initializeDashboard();
    initializeResponsiveTables();
});

/**
 * Initialize dashboard components
 */
function initializeDashboard() {
    // Show welcome modal if user has no pets (controlled by PHP variable)
    if (typeof showWelcomeModal !== 'undefined' && showWelcomeModal) {
        $('#welcomePetModal').modal('show');
    }
    
    // Initialize other dashboard components
    initializeTooltips();
    initializeAnimations();
}

/**
 * Handle skipping pet registration
 */
function skipRegistration() {
    $('#welcomePetModal').modal('hide');
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * Initialize card animations
 */
function initializeAnimations() {
    // Add entrance animations to cards
    $('.enhanced-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
        $(this).addClass('animate-fade-in');
    });
}

/**
 * Show notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, info, warning, danger)
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            ${message}
        </div>
    `);
    
    // Add to body
    $('body').append(notification);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}

/**
 * Handle reservation button clicks
 */
$(document).on('click', '.btn-reservation', function(e) {
    // Add loading state
    const $btn = $(this);
    const originalText = $btn.html();
    
    $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...');
    $btn.prop('disabled', true);
    
    // Restore button after a short delay (simulating loading)
    setTimeout(function() {
        $btn.html(originalText);
        $btn.prop('disabled', false);
    }, 1000);
});

/**
 * Handle responsive table scrolling
 */
function initializeResponsiveTables() {
    $('.table-responsive').each(function() {
        const $table = $(this);
        
        // Add scroll indicators for mobile
        if ($(window).width() < 768) {
            $table.addClass('show-scroll-hint');
        }
    });
}

/**
 * Window resize handler
 */
$(window).on('resize', function() {
    initializeResponsiveTables();
});

/**
 * Handle category card hover effects
 */
$(document).on('mouseenter', '.enhanced-card', function() {
    $(this).addClass('card-hover-effect');
});

$(document).on('mouseleave', '.enhanced-card', function() {
    $(this).removeClass('card-hover-effect');
});

/**
 * Handle laboratory list interactions
 */
$(document).on('click', '.lab-list .list-group-item', function() {
    $(this).addClass('selected-lab');
    $(this).siblings().removeClass('selected-lab');
});

/**
 * Smooth scroll to sections
 */
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Handle form validation for pet registration
 */
function validatePetForm() {
    // This function can be used when the pet registration form is integrated
    // For now, it's a placeholder for future implementation
    return true;
}

/**
 * Handle modal events
 */
$('#welcomePetModal').on('shown.bs.modal', function() {
    // Focus on the register button when modal is shown
    $(this).find('.btn-primary').focus();
});

$('#welcomePetModal').on('hidden.bs.modal', function() {
    // Store that the modal was dismissed
    sessionStorage.setItem('welcomeModalDismissed', 'true');
});

/**
 * Check if welcome modal should be shown
 */
function shouldShowWelcomeModal() {
    return typeof showWelcomeModal !== 'undefined' && 
           showWelcomeModal && 
           !sessionStorage.getItem('welcomeModalDismissed');
}

/**
 * Initialize dashboard on page load
 */
$(document).ready(function() {
    // Check if welcome modal should be shown
    if (shouldShowWelcomeModal()) {
        $('#welcomePetModal').modal('show');
    }
});

/**
 * Handle category selection
 */
$(document).on('click', '.category-selector', function() {
    const categoryId = $(this).data('category-id');
    // Handle category selection logic here
    console.log('Selected category:', categoryId);
});

/**
 * Format currency display
 */
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Update laboratory prices dynamically
 */
function updateLabPrices() {
    $('.lab-price').each(function() {
        const price = $(this).text().replace('₱', '').replace(',', '');
        $(this).text(formatCurrency(price));
    });
}

/**
 * Handle search functionality for laboratories
 */
function searchLaboratories(searchTerm) {
    const term = searchTerm.toLowerCase();
    
    $('.lab-list .list-group-item').each(function() {
        const labName = $(this).find('span').first().text().toLowerCase();
        
        if (labName.includes(term)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

/**
 * Add search functionality
 */
$(document).on('keyup', '.lab-search', function() {
    const searchTerm = $(this).val();
    searchLaboratories(searchTerm);
});

/**
 * Handle print functionality
 */
function printPage() {
    window.print();
}

/**
 * Handle export functionality
 */
function exportData(format) {
    // Placeholder for export functionality
    console.log('Exporting data in format:', format);
    showNotification('Export functionality will be implemented soon.', 'info');
}

/**
 * Initialize tooltips and popovers
 */
function initializeUIComponents() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
}

/**
 * Handle keyboard shortcuts
 */
$(document).on('keydown', function(e) {
    // Ctrl + P for print
    if (e.ctrlKey && e.which === 80) {
        e.preventDefault();
        printPage();
    }
    
    // Escape key to close modals
    if (e.which === 27) {
        $('.modal').modal('hide');
    }
});

/**
 * Initialize everything when document is ready
 */
$(document).ready(function() {
    initializeUIComponents();
    updateLabPrices();
    
    // Add loading animation
    $('body').addClass('loaded');
});

/**
 * Handle page visibility changes
 */
$(document).on('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden
        console.log('Page is hidden');
    } else {
        // Page is visible
        console.log('Page is visible');
    }
});

/**
 * Cleanup function
 */
function cleanup() {
    // Remove event listeners and clear timeouts
    $(window).off('resize');
    $('.modal').off('shown.bs.modal hidden.bs.modal');
    
    // Clear any running intervals or timeouts
    // This is a placeholder for cleanup operations
}

/**
 * Initialize cleanup on page unload
 */
$(window).on('beforeunload', function() {
    cleanup();
});