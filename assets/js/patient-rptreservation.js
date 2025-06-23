/**
 * Appointments Page JavaScript
 * Handles appointment management functionality including cancellation
 */

// Google Analytics Configuration
window.dataLayer = window.dataLayer || [];
function gtag(){
    dataLayer.push(arguments);
}
gtag('js', new Date());
gtag('config', 'UA-94034622-3');

/**
 * Cancel a reservation with confirmation
 * @param {number} reservationId - The ID of the reservation to cancel
 */
function cancelReservation(reservationId) {
    if (confirm('Are you sure you want to cancel this reservation? This action cannot be undone.')) {
        // Create a form and submit it
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Add cancel_reservation input
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cancel_reservation';
        input.value = '1';
        form.appendChild(input);
        
        // Add reservation_id input
        var reservationInput = document.createElement('input');
        reservationInput.type = 'hidden';
        reservationInput.name = 'reservation_id';
        reservationInput.value = reservationId;
        form.appendChild(reservationInput);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}

/**
 * Initialize page functionality when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables if the table exists
    if (document.getElementById('table-1')) {
        // DataTables initialization will be handled by the existing modules-datatables.js
        console.log('Appointments table ready for DataTables initialization');
    }
    
    // Additional initialization code can be added here
    console.log('Appointments page JavaScript loaded successfully');
});

/**
 * Handle form validation for add laboratory modal
 */
function validateLaboratoryForm() {
    const form = document.querySelector('form[method="POST"]');
    if (!form) return true;
    
    const laboratory = form.querySelector('input[name="laboratory"]').value.trim();
    const categoryId = form.querySelector('select[name="category_id"]').value;
    const price = form.querySelector('input[name="price"]').value;
    const capacity = form.querySelector('input[name="capacity"]').value;
    
    if (!laboratory) {
        alert('Please enter a laboratory name');
        return false;
    }
    
    if (!categoryId) {
        alert('Please select a category');
        return false;
    }
    
    if (!price || parseFloat(price) <= 0) {
        alert('Please enter a valid price');
        return false;
    }
    
    if (!capacity || parseInt(capacity) <= 0) {
        alert('Please enter a valid capacity');
        return false;
    }
    
    return true;
}

/**
 * Format currency for display
 * @param {number} amount - The amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date for display
 * @param {string} dateString - The date string to format
 * @returns {string} Formatted date string
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

/**
 * Format time for display
 * @param {string} timeString - The time string to format
 * @returns {string} Formatted time string
 */
function formatTime(timeString) {
    if (!timeString || timeString === '00:00:00') {
        return '<span style="color: #6c757d; font-style: italic;">Not set</span>';
    }
    
    const time = new Date('1970-01-01T' + timeString);
    return time.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}