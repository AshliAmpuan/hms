    function editItem(id, name, category, quantity, details, price, active) {
        document.getElementById('edit_item_id').value = id;
        document.getElementById('edit_item_name').value = name;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_quantity').value = quantity;
        document.getElementById('edit_details').value = details;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_active').value = active;
    }

    function deleteItem(id, name) {
        document.getElementById('delete_item_id').value = id;
        document.getElementById('delete_item_name').textContent = name;
    }
