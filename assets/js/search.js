// Enhanced Search Functionality for Pet Store
$(document).ready(function() {
    // Cache DOM elements for better performance
    const $searchInput = $('#productSearch');
    const $clearSearch = $('#clearSearch');
    const $productRows = $('.product-row');
    const $noResults = $('#noResults');
    const $productsTableBody = $('#productsTableBody');
    
    // Show/hide clear button based on input
    function toggleClearButton() {
        if ($searchInput.val().trim() !== '') {
            $clearSearch.show();
        } else {
            $clearSearch.hide();
        }
    }
    
    // Perform the search filtering
    function performSearch() {
        const searchTerm = $searchInput.val().toLowerCase().trim();
        let visibleRows = 0;
        
        if (searchTerm === '') {
            // Show all rows if search is empty
            $productRows.show();
            $noResults.hide();
            visibleRows = $productRows.length;
        } else {
            // Filter rows based on search term
            $productRows.each(function() {
                const $row = $(this);
                const itemName = $row.data('name') || '';
                const category = $row.data('category') || '';
                
                // Check if search term matches first letter(s) of item name or category
                if (itemName.toLowerCase().startsWith(searchTerm) || category.toLowerCase().startsWith(searchTerm)) {
                    $row.show();
                    visibleRows++;
                } else {
                    $row.hide();
                }
            });
            
            // Show "no results" message if no products match
            if (visibleRows === 0) {
                $noResults.show();
            } else {
                $noResults.hide();
            }
        }
        
        // Update row numbers for visible rows
        updateRowNumbers();
        // Clear button stays hidden during typing
    }
    
    // Update row numbers after filtering
    function updateRowNumbers() {
        let counter = 1;
        $productRows.each(function() {
            if ($(this).is(':visible')) {
                $(this).find('td:first-child').text(counter);
                counter++;
            }
        });
    }
    
    // Clear search functionality
    function clearSearch() {
        $searchInput.val('').focus();
        performSearch();
    }
    
    // Event listeners
    $searchInput.on('input keyup', function(e) {
        // Debounce search for better performance
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(performSearch, 150);
        
        // Handle Enter key
        if (e.keyCode === 13) {
            clearTimeout(this.searchTimeout);
            performSearch();
        }
    });
    
    // Clear search button click
    $clearSearch.on('click', clearSearch);
    
    // Handle Escape key to clear search
    $searchInput.on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            clearSearch();
        }
    });
    
    // Add keyboard navigation for better UX
    let selectedRow = -1;
    
    $searchInput.on('keydown', function(e) {
        const $visibleRows = $productRows.filter(':visible');
        
        switch(e.keyCode) {
            case 40: // Arrow Down
                e.preventDefault();
                selectedRow = Math.min(selectedRow + 1, $visibleRows.length - 1);
                highlightSelectedRow($visibleRows);
                break;
            case 38: // Arrow Up
                e.preventDefault();
                selectedRow = Math.max(selectedRow - 1, -1);
                highlightSelectedRow($visibleRows);
                break;
            case 13: // Enter
                if (selectedRow >= 0) {
                    e.preventDefault();
                    const $selectedRow = $visibleRows.eq(selectedRow);
                    $selectedRow.find('.btn-add-cart').first().click();
                }
                break;
        }
    });
    
    function highlightSelectedRow($visibleRows) {
        $visibleRows.removeClass('table-active');
        if (selectedRow >= 0) {
            $visibleRows.eq(selectedRow).addClass('table-active');
        }
    }
    
    // Reset selection when search changes
    $searchInput.on('input', function() {
        selectedRow = -1;
        $productRows.removeClass('table-active');
    });
    
    // Initial setup - hide clear button on page load
    $clearSearch.hide();
});