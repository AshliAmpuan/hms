/**
 * Cashier Appointment JavaScript Functions
 * Handles payment calculations and change display
 */

/**
 * Calculate change amount for payment processing
 * @param {number} reservationId - The reservation ID for the payment
 */
function calculateChange(reservationId) {
    const amountToPay = parseFloat(document.getElementById('atp' + reservationId).value);
    const amountReceived = parseFloat(document.getElementById('amount' + reservationId).value);
    
    if (!isNaN(amountReceived) && amountReceived > 0) {
        const change = amountReceived - amountToPay;
        if (change >= 0) {
            document.getElementById('changeAmount' + reservationId).textContent = change.toFixed(2);
            document.getElementById('changeDisplay' + reservationId).style.display = 'block';
        } else {
            document.getElementById('changeDisplay' + reservationId).style.display = 'none';
        }
    } else {
        document.getElementById('changeDisplay' + reservationId).style.display = 'none';
    }
}