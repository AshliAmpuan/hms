function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const receiptContent = document.getElementById('receipt-content');
    
    // Show loading message
    const originalContent = document.querySelector('.action-buttons').innerHTML;
    document.querySelector('.action-buttons').innerHTML = '<p>Generating PDF, please wait...</p>';
    
    html2canvas(receiptContent, {
        scale: 2,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        const imgWidth = 210;
        const pageHeight = 295;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        
        let position = 0;
        
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        
        // Generate filename with order number and date
        const orderNumber = getOrderNumber(); // This will need to be passed from PHP
        const currentDate = new Date().toISOString().slice(0, 10);
        const filename = `Receipt_${orderNumber}_${currentDate}.pdf`;
        
        pdf.save(filename);
        
        // Restore original buttons
        document.querySelector('.action-buttons').innerHTML = originalContent;
    }).catch(error => {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again or use the print option.');
        document.querySelector('.action-buttons').innerHTML = originalContent;
    });
}

// Alternative download as HTML file
function downloadHTML() {
    const receiptContent = document.getElementById('receipt-content').outerHTML;
    const fullHTML = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - ${getOrderNumber()}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .receipt-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .receipt-title { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .order-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .customer-info, .order-details { width: 48%; }
        .info-section { margin-bottom: 15px; }
        .info-label { font-weight: bold; margin-bottom: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .receipt-footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    ${receiptContent}
</body>
</html>`;
    
    const blob = new Blob([fullHTML], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    const orderNumber = getOrderNumber();
    const currentDate = new Date().toISOString().slice(0, 10);
    
    a.href = url;
    a.download = `Receipt_${orderNumber}_${currentDate}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Helper function to get order number (will be set from PHP)
function getOrderNumber() {
    return window.orderNumber || 'UNKNOWN';
}