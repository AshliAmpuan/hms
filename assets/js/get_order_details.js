/**
 * Order Details JavaScript Functions
 */

$(document).ready(function() {
    // Initialize order details functionality
    initOrderDetails();
});

function initOrderDetails() {
    // Add smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');
    
    // Add tooltips to status badges
    initStatusTooltips();
    
    // Add print functionality
    addPrintButton();
    
    // Add item row interactions
    initItemRowInteractions();
    
    // Add responsive table handling
    handleResponsiveTable();
}

function initStatusTooltips() {
    // Add tooltips to status badges for better user understanding
    $('.status-badge').each(function() {
        const status = $(this).text().toLowerCase();
        let tooltipText = '';
        
        switch(status) {
            case 'pending':
                tooltipText = 'Order is being processed';
                break;
            case 'completed':
                tooltipText = 'Order has been fulfilled';
                break;
            case 'cancelled':
                tooltipText = 'Order was cancelled';
                break;
            case 'refunded':
                tooltipText = 'Order has been refunded';
                break;
            case 'paid':
                tooltipText = 'Payment received';
                break;
            case 'unpaid':
                tooltipText = 'Payment pending';
                break;
            case 'partial':
                tooltipText = 'Partial payment received';
                break;
        }
        
        if (tooltipText) {
            $(this).attr('title', tooltipText).tooltip();
        }
    });
}

function addPrintButton() {
    // Add a print button to the order details
    const printButton = `
        <div class="text-right mb-3">
            <button type="button" class="btn btn-outline-primary btn-sm" id="printOrderBtn">
                <i class="fas fa-print"></i> Print Order
            </button>
        </div>
    `;
    
    $('.order-details-container').prepend(printButton);
    
    // Handle print button click
    $('#printOrderBtn').click(function() {
        printOrderDetails();
    });
}

function printOrderDetails() {
    // Create a print-friendly version of the order details
    const printContent = $('.order-details-container').clone();
    
    // Remove print button from print content
    printContent.find('#printOrderBtn').parent().remove();
    
    // Create print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Details - Print</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
            <link href="css/order_details.css" rel="stylesheet">
            <style>
                @media print {
                    body { font-size: 12px; }
                    .order-details-container { 
                        box-shadow: none; 
                        padding: 10px; 
                    }
                    .status-badge { 
                        border: 1px solid #000 !important; 
                        -webkit-print-color-adjust: exact;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                ${printContent.html()}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for content to load then print
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function initItemRowInteractions() {
    // Add click interaction to item rows for better UX
    $('.item-row').hover(
        function() {
            $(this).addClass('table-hover-highlight');
        },
        function() {
            $(this).removeClass('table-hover-highlight');
        }
    );
    
    // Add click to expand item details if truncated
    $('.item-details').click(function() {
        const $this = $(this);
        if ($this.hasClass('expanded')) {
            $this.removeClass('expanded');
        } else {
            $this.addClass('expanded');
        }
    });
}

function handleResponsiveTable() {
    // Make table more responsive on small screens
    function adjustTableForMobile() {
        if ($(window).width() < 768) {
            $('.items-table').addClass('table-responsive-sm');
            
            // Add mobile-friendly class
            $('.order-details-container').addClass('mobile-view');
        } else {
            $('.items-table').removeClass('table-responsive-sm');
            $('.order-details-container').removeClass('mobile-view');
        }
    }
    
    // Check on load and resize
    adjustTableForMobile();
    $(window).resize(adjustTableForMobile);
}

// Utility function to format currency
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Function to calculate and verify totals (for validation)
function validateOrderTotals() {
    let calculatedSubtotal = 0;
    
    $('.item-row').each(function() {
        const totalText = $(this).find('.total-price').text().replace('₱', '').replace(',', '');
        const total = parseFloat(totalText);
        if (!isNaN(total)) {
            calculatedSubtotal += total;
        }
    });
    
    const displayedSubtotal = parseFloat($('.subtotal-amount').text().replace('₱', '').replace(',', ''));
    const displayedTotal = parseFloat($('.final-total').text().replace('₱', '').replace(',', ''));
    
    // Log any discrepancies (for debugging)
    if (Math.abs(calculatedSubtotal - displayedSubtotal) > 0.01) {
        console.warn('Subtotal mismatch:', calculatedSubtotal, 'vs', displayedSubtotal);
    }
    
    if (Math.abs(calculatedSubtotal - displayedTotal) > 0.01) {
        console.warn('Total mismatch:', calculatedSubtotal, 'vs', displayedTotal);
    }
}

// Animation functions for enhanced UX
function animateStatusChange(element, newStatus) {
    $(element).fadeOut(200, function() {
        $(this).removeClass().addClass('status-badge status-' + newStatus);
        $(this).text(newStatus.toUpperCase()).fadeIn(200);
    });
}

function highlightTotal() {
    $('.final-total').addClass('highlight-animation');
    setTimeout(() => {
        $('.final-total').removeClass('highlight-animation');
    }, 1000);
}

// Export functions for potential use in other scripts
window.OrderDetails = {
    formatCurrency: formatCurrency,
    validateTotals: validateOrderTotals,
    animateStatusChange: animateStatusChange,
    highlightTotal: highlightTotal,
    print: printOrderDetails
};