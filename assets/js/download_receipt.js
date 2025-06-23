// Order Receipt JavaScript Functions

/**
 * Download receipt as PDF
 */
function downloadPDF() {
    // Check if jsPDF is loaded
    if (typeof window.jsPDF === 'undefined') {
        alert('PDF library not loaded. Please try again.');
        return;
    }

    // Get the receipt content
    const receiptContent = document.getElementById('receipt-content');
    
    if (!receiptContent) {
        alert('Receipt content not found.');
        return;
    }

    // Show loading state
    const originalContent = receiptContent.innerHTML;
    const loadingDiv = document.createElement('div');
    loadingDiv.style.cssText = 'text-align: center; padding: 50px; font-size: 16px; color: #666;';
    loadingDiv.innerHTML = 'Generating PDF...';
    
    // Temporarily replace content with loading message
    receiptContent.innerHTML = '';
    receiptContent.appendChild(loadingDiv);

    // Use html2canvas to capture the receipt
    html2canvas(receiptContent, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff',
        width: receiptContent.offsetWidth,
        height: receiptContent.offsetHeight
    }).then(canvas => {
        // Restore original content
        receiptContent.innerHTML = originalContent;
        
        // Create PDF
        const { jsPDF } = window.jsPDF;
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        // Calculate dimensions
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        
        let position = 0;
        
        // Add image to PDF
        const imgData = canvas.toDataURL('image/png');
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        
        // Add new pages if content is longer than one page
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        
        // Generate filename
        const orderNumber = window.orderNumber || 'receipt';
        const filename = `receipt-${orderNumber}-${getCurrentDate()}.pdf`;
        
        // Save the PDF
        pdf.save(filename);
        
    }).catch(error => {
        // Restore original content on error
        receiptContent.innerHTML = originalContent;
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again or use the print option.');
    });
}

/**
 * Get current date in YYYY-MM-DD format
 */
function getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Print receipt
 */
function printReceipt() {
    window.print();
}

/**
 * Close window
 */
function closeWindow() {
    if (window.opener) {
        window.close();
    } else {
        // If window wasn't opened by JavaScript, redirect to a previous page
        if (document.referrer) {
            window.location.href = document.referrer;
        } else {
            window.history.back();
        }
    }
}

/**
 * Initialize receipt page
 */
function initializeReceipt() {
    // Add event listeners to buttons if they exist
    const downloadBtn = document.querySelector('[onclick="downloadPDF()"]');
    const printBtn = document.querySelector('[onclick="window.print()"]');
    const closeBtn = document.querySelector('[onclick="window.close()"]');
    
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            downloadPDF();
        });
    }
    
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            printReceipt();
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeWindow();
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            printReceipt();
        }
        
        // Ctrl+S for download PDF
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            downloadPDF();
        }
        
        // Escape to close
        if (e.key === 'Escape') {
            closeWindow();
        }
    });
    
    // Check if libraries are loaded
    checkLibraries();
}

/**
 * Check if required libraries are loaded
 */
function checkLibraries() {
    let missingLibraries = [];
    
    if (typeof window.jsPDF === 'undefined') {
        missingLibraries.push('jsPDF');
    }
    
    if (typeof html2canvas === 'undefined') {
        missingLibraries.push('html2canvas');
    }
    
    if (missingLibraries.length > 0) {
        console.warn('Missing libraries:', missingLibraries.join(', '));
        console.warn('PDF download functionality may not work properly.');
    }
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeReceipt);

// Fallback initialization for older browsers
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeReceipt);
} else {
    initializeReceipt();
}