/**
 * Order History Page JavaScript Functions
 */

// Global variables
let orderDetailsModal;
let dataTable;

/**
 * Initialize the page when DOM is ready
 */
$(document).ready(function() {
    initializeDataTable();
    initializeModal();
});

/**
 * Initialize DataTable with custom settings
 */
function initializeDataTable() {
    if (!$.fn.DataTable.isDataTable('#table-1')) {
        dataTable = $('#table-1').DataTable({
            "order": [[ 2, "desc" ]], // Sort by date column (index 2) descending
            "pageLength": 25,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": [7] } // Disable sorting on Actions column
            ],
            "language": {
                "emptyTable": "No orders found",
                "info": "Showing _START_ to _END_ of _TOTAL_ orders",
                "infoEmpty": "Showing 0 to 0 of 0 orders",
                "lengthMenu": "Show _MENU_ orders per page",
                "search": "Search orders:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "drawCallback": function(settings) {
                // Re-initialize tooltips after table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
}

/**
 * Initialize modal
 */
function initializeModal() {
    orderDetailsModal = $('#orderDetailsModal');
}

/**
 * View order details in modal
 * @param {number} orderId - The order ID to fetch details for
 */
function viewOrderDetails(orderId) {
    if (!orderId) {
        showAlert('Error: Invalid order ID', 'danger');
        return;
    }

    // Show loading state
    const modalContent = $('#orderDetailsContent');
    modalContent.html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Loading order details...</div>');
    
    // Show modal immediately with loading state
    orderDetailsModal.modal('show');

    // Fetch order details
    $.ajax({
        url: 'get_order_details.php',
        method: 'POST',
        data: { 
            order_id: orderId 
        },
        timeout: 30000, // 30 second timeout
        success: function(response) {
            if (response && response.trim() !== '') {
                modalContent.html(response);
            } else {
                modalContent.html('<div class="alert alert-warning">No details available for this order.</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            let errorMessage = 'Error loading order details. ';
            if (status === 'timeout') {
                errorMessage += 'Request timed out.';
            } else if (xhr.status === 404) {
                errorMessage += 'Order details not found.';
            } else if (xhr.status === 500) {
                errorMessage += 'Server error occurred.';
            } else {
                errorMessage += 'Please try again later.';
            }
            
            modalContent.html(`<div class="alert alert-danger">${errorMessage}</div>`);
        }
    });
}

/**
 * Download receipt for completed order
 * @param {number} orderId - The order ID to download receipt for
 */
function downloadReceipt(orderId) {
    if (!orderId) {
        showAlert('Error: Invalid order ID', 'danger');
        return;
    }

    try {
        // Create a temporary link and trigger download
        const downloadUrl = `download_receipt.php?order_id=${orderId}`;
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.target = '_blank';
        link.download = `receipt_${orderId}.pdf`; // Suggest filename
        
        // Append to body, click, and remove
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        showAlert('Receipt download started', 'success');
        
    } catch (error) {
        console.error('Download error:', error);
        showAlert('Error downloading receipt', 'danger');
    }
}

/**
 * Show alert message to user
 * @param {string} message - The message to display
 * @param {string} type - Alert type (success, danger, warning, info)
 */
function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insert alert at the top of the section body
    $('.section-body').prepend(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

/**
 * Refresh the order table
 */
function refreshOrderTable() {
    if (dataTable) {
        dataTable.ajax.reload(null, false); // Reload without resetting pagination
    } else {
        location.reload(); // Fallback to page reload
    }
}

/**
 * Filter orders by status
 * @param {string} status - Status to filter by (empty string for all)
 */
function filterByStatus(status = '') {
    if (dataTable) {
        dataTable.column(5).search(status).draw(); // Column 5 is the status column
    }
}

/**
 * Export order history to CSV
 */
function exportToCSV() {
    if (dataTable) {
        // Get visible data
        const data = dataTable.rows({ search: 'applied' }).data().toArray();
        const headers = ['Order Number', 'Date', 'Items', 'Amount', 'Status', 'Payment Method'];
        
        let csvContent = headers.join(',') + '\n';
        
        data.forEach(row => {
            const csvRow = [
                `"${$(row[1]).text()}"`, // Order number
                `"${$(row[2]).text()}"`, // Date
                `"${$(row[3]).text()}"`, // Items
                `"${$(row[4]).text()}"`, // Amount
                `"${$(row[5]).text()}"`, // Status
                `"${$(row[6]).text()}"`, // Payment method
            ].join(',');
            csvContent += csvRow + '\n';
        });
        
        // Create and download file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `order_history_${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
        
        showAlert('Order history exported successfully', 'success');
    }
}

/**
 * Handle modal close events
 */
$(document).on('hidden.bs.modal', '#orderDetailsModal', function () {
    // Clear modal content when closed
    $('#orderDetailsContent').html('');
});

/**
 * Handle escape key to close modal
 */
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && orderDetailsModal && orderDetailsModal.hasClass('show')) {
        orderDetailsModal.modal('hide');
    }
});

/**
 * Add tooltips to action buttons
 */
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});