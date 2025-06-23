/**
 * Pet Store JavaScript Functions
 */

$(document).ready(function() {
    // Initialize DataTable with proper checking
    initializeDataTable();
    
    // Initialize payment method handling
    initializePaymentHandling();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize tooltips
    initializeTooltips();
});

/**
 * Initialize DataTable with proper error handling
 */
function initializeDataTable() {
    // Check if DataTable already exists and destroy it first
    if ($.fn.DataTable.isDataTable('#table-1')) {
        $('#table-1').DataTable().destroy();
    }
    
    // Clear any existing DataTable classes/elements
    $('#table-1').removeClass('dataTable');
    
    // Initialize fresh DataTable
    $('#table-1').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 10,
        "language": {
            "search": "Search products:",
            "emptyTable": "No products available",
            "zeroRecords": "No matching products found"
        },
        "destroy": true // This ensures the table can be reinitialized
    });
}

/**
 * Show product details in modal
 * @param {string} itemName - The name of the item
 * @param {string} details - The details of the item
 */
function showDetails(itemName, details) {
    $('#detailsModalTitle').text(itemName + ' - Details');
    
    // Format details with proper line breaks
    const formattedDetails = details.replace(/\n/g, '<br>');
    $('#detailsModalContent').html('<div class="product-details-content">' + formattedDetails + '</div>');
    
    // Show the modal
    $('#detailsModal').modal('show');
}

/**
 * Initialize payment method selection handling
 */
function initializePaymentHandling() {
    // Remove existing event handlers to prevent duplicates
    $('#mop').off('change.paymentHandler');
    $('#paymentModal').off('hidden.bs.modal.paymentHandler');
    
    $('#mop').on('change.paymentHandler', function() {
        const selectedMethod = $(this).val();
        $('#selected_mop').val(selectedMethod);
        
        // Show/hide payment options based on selection
        if (selectedMethod === '1') {
            // Cash payment
            $('#modalfooter').show();
            $('#paypal-button-container').hide();
        } else if (selectedMethod === '2') {
            // PayPal payment
            $('#paypal-button-container').show();
            $('#modalfooter').hide();
        } else {
            // No valid selection
            $('#modalfooter').show();
            $('#paypal-button-container').hide();
            $('#confirm_payment').prop('disabled', true);
        }
    });

    // Reset modal when closed
    $('#paymentModal').on('hidden.bs.modal.paymentHandler', function() {
        $('#mop').val('#');
        $('#selected_mop').val('');
        $('#paypal-button-container').hide();
        $('#modalfooter').show();
        $('#confirm_payment').prop('disabled', false);
    });
}

/**
 * Initialize PayPal buttons
 * @param {number} totalPrice - Total cart price
 */
function initializePayPal(totalPrice) {
    if (typeof paypal === 'undefined' || totalPrice <= 0) {
        console.warn('PayPal SDK not loaded or invalid total price');
        return;
    }

    // Clear existing PayPal buttons
    $('#paypal-button-container').empty();

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        currency_code: "PHP",
                        value: parseFloat(totalPrice).toFixed(2)
                    },
                    description: "Pet Store Purchase"
                }]
            });
        },
        
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Show loading state
                showPaymentProcessing();
                
                // Submit the checkout form for PayPal
                submitPayPalCheckout();
            });
        },
        
        onError: function(err) {
            console.error('PayPal Error:', err);
            showPaymentError('PayPal payment failed. Please try again or select a different payment method.');
        },
        
        onCancel: function(data) {
            console.log('PayPal payment cancelled by user');
            hidePaymentProcessing();
        }
    }).render('#paypal-button-container');
}

/**
 * Submit PayPal checkout form
 */
function submitPayPalCheckout() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    
    // Add finalize_checkout input
    const input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = 'finalize_checkout';
    input1.value = '1';
    form.appendChild(input1);
    
    // Add payment method input
    const input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'mop';
    input2.value = '2';
    form.appendChild(input2);
    
    document.body.appendChild(form);
    form.submit();
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    // Remove existing handlers to prevent duplicates
    $('input[name="quantity"]').off('input.validation');
    $('form').off('submit.validation');
    
    // Validate quantity inputs
    $('input[name="quantity"]').on('input.validation', function() {
        const value = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));
        const min = parseInt($(this).attr('min'));
        
        if (value > max) {
            $(this).val(max);
            showToast('Maximum available quantity is ' + max, 'warning');
        } else if (value < min) {
            $(this).val(min);
        }
    });

    // Prevent form submission with invalid quantities
    $('form').on('submit.validation', function(e) {
        const quantityInputs = $(this).find('input[name="quantity"]');
        let isValid = true;
        
        quantityInputs.each(function() {
            const value = parseInt($(this).val());
            const max = parseInt($(this).attr('max'));
            const min = parseInt($(this).attr('min'));
            
            if (value > max || value < min || isNaN(value)) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showToast('Please check the quantity values', 'error');
        }
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    // Destroy existing tooltips first
    $('[data-toggle="tooltip"]').tooltip('dispose');
    
    // Initialize fresh tooltips
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * Show payment processing state
 */
function showPaymentProcessing() {
    $('#paypal-button-container').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Processing payment...</div>');
}

/**
 * Hide payment processing state
 */
function hidePaymentProcessing() {
    // PayPal buttons will be re-rendered when modal is reopened
}

/**
 * Show payment error message
 * @param {string} message - Error message to display
 */
function showPaymentError(message) {
    // Remove existing error alerts first
    $('.modal-body .alert-danger').remove();
    
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('.modal-body').prepend(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert-danger').alert('close');
    }, 5000);
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, warning, error, info)
 */
function showToast(message, type = 'info') {
    const toastClass = {
        'success': 'alert-success',
        'warning': 'alert-warning',
        'error': 'alert-danger',
        'info': 'alert-info'
    };
    
    const toast = `
        <div class="alert ${toastClass[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('body').append(toast);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        $('.alert').last().alert('close');
    }, 3000);
}

/**
 * Confirm cart item removal
 * @param {Event} e - Event object
 * @param {string} itemName - Name of item to remove
 */
function confirmRemoveItem(e, itemName) {
    e.preventDefault();
    
    if (confirm(`Are you sure you want to remove "${itemName}" from your cart?`)) {
        e.target.closest('form').submit();
    }
}

/**
 * Update cart item quantity (if implementing AJAX updates)
 * @param {number} itemId - Item ID
 * @param {number} newQuantity - New quantity
 */
function updateCartQuantity(itemId, newQuantity) {
    // This function can be implemented for AJAX cart updates
    // Currently the system uses form submissions
    console.log(`Update item ${itemId} quantity to ${newQuantity}`);
}

/**
 * Clear all cart items
 */
function clearCart() {
    if (confirm('Are you sure you want to clear all items from your cart?')) {
        // Implementation would depend on backend support
        console.log('Clear cart functionality');
    }
}

/**
 * Utility function to reinitialize all components (useful for SPA navigation)
 */
function reinitializeComponents() {
    initializeDataTable();
    initializePaymentHandling();
    initializeFormValidation();
    initializeTooltips();
}

// Export for potential use in other scripts
window.petStoreUtils = {
    reinitializeComponents: reinitializeComponents,
    initializeDataTable: initializeDataTable
};