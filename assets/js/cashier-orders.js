// Orders Management JavaScript Functions

/**
 * Calculate change amount for payment
 * @param {number} orderId - The order ID
 */
function calculateChange(orderId) {
  const amountToPay = parseFloat(document.getElementById('atp' + orderId).value);
  const amountReceived = parseFloat(document.getElementById('amount' + orderId).value);
  const changeDisplay = document.getElementById('changeDisplay' + orderId);
  const changeAmount = document.getElementById('changeAmount' + orderId);
  
  if (!isNaN(amountReceived) && amountReceived > 0) {
    const change = amountReceived - amountToPay;
    changeAmount.textContent = change.toFixed(2);
    changeDisplay.style.display = 'block';
    
    // Change color based on if it's positive or negative
    if (change >= 0) {
      changeDisplay.style.color = '#28a745'; // Green for positive change
      changeDisplay.style.backgroundColor = '#d4edda';
      changeDisplay.style.borderColor = '#c3e6cb';
    } else {
      changeDisplay.style.color = '#dc3545'; // Red for insufficient amount
      changeDisplay.style.backgroundColor = '#f8d7da';
      changeDisplay.style.borderColor = '#f5c6cb';
      changeAmount.textContent = Math.abs(change).toFixed(2) + ' (Insufficient)';
    }
  } else {
    changeDisplay.style.display = 'none';
  }
}

/**
 * Load and display order details in modal
 * @param {number} orderId - The order ID
 */
function viewOrderDetails(orderId) {
  $.ajax({
    url: 'get_order_details.php',
    method: 'POST',
    data: { order_id: orderId },
    success: function(response) {
      $('#orderDetailsContent').html(response);
      $('#orderDetailsModal').modal('show');
    },
    error: function() {
      alert('Error loading order details');
    }
  });
}

// Document ready function
$(document).ready(function() {
  // Initialize DataTable with custom settings
  if (!$.fn.DataTable.isDataTable('#table-1')) {
    $('#table-1').DataTable({
      "order": [[ 5, "desc" ]], // Sort by date column (index 5) descending
      "pageLength": 25,
      "responsive": true,
      "columnDefs": [
        { "orderable": false, "targets": [4, 8] } // Disable sorting on Order Items and Status columns
      ]
    });
  }
  
  // Initialize tooltips
  $('[data-toggle="tooltip"]').tooltip();
});