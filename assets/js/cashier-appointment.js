/**
 * Appointment Page JavaScript Functions
 */

/**
 * Calculate change amount for payment processing
 * @param {number} reservationId - The reservation ID
 */
function calculateChange(reservationId) {
    const amountToPay = parseFloat(document.getElementById('atp' + reservationId).value);
    const amountReceived = parseFloat(document.getElementById('amount' + reservationId).value);
    const changeDisplay = document.getElementById('changeDisplay' + reservationId);
    const changeAmount = document.getElementById('changeAmount' + reservationId);
    
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