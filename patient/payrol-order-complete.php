<?php 

include('../include/connection.php');

session_start();

if(isset($_POST['mop'])) {
    $patient_id = $_SESSION['patient_id'];

    date_default_timezone_set('Asia/Manila');
    $tdate = date("Y-m-d");

    // Get cart items for order processing
    $countCart = mysqli_query($con, "SELECT shopping_cart.id as Id, inventory.price, shopping_cart.inventory_id as InventoryId, shopping_cart.quantity, inventory.item_name, item_category.category_name FROM shopping_cart INNER JOIN inventory ON inventory.id=shopping_cart.inventory_id INNER JOIN item_category ON item_category.id=inventory.category_id WHERE shopping_cart.patient_id = $patient_id");

    $total_amount = 0;
    $total_items = 0;
    $order_items = array();

    while($row = mysqli_fetch_array($countCart))
    {
        $cart_id = $row['Id'];
        $inventory_id = $row['InventoryId'];
        $item_price = $row['price'];
        $quantity = $row['quantity'];
        $item_name = $row['item_name'];
        $category_name = $row['category_name'];
        $mop = $_POST['mop'];

        $line_total = $item_price * $quantity;
        $total_amount += $line_total;
        $total_items += $quantity;

        $order_items[] = array(
            'inventory_id' => $inventory_id,
            'item_name' => $item_name,
            'category_name' => $category_name,
            'quantity' => $quantity,
            'unit_price' => $item_price,
            'total_price' => $line_total
        );
    }

    // Generate unique order number
    $order_number = 'ORD-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    // Insert main order
    mysqli_query($con, "INSERT INTO orders (`patient_id`, `order_number`, `total_amount`, `total_items`, `order_status`, `payment_status`, `mop`, `order_date`) VALUES ('$patient_id', '$order_number', '$total_amount', '$total_items', 'completed', 'paid', '$mop', NOW())");

    $order_id = mysqli_insert_id($con);

    // Insert order items
    foreach($order_items as $item) {
        mysqli_query($con, "INSERT INTO order_items (`order_id`, `inventory_id`, `item_name`, `category_name`, `quantity`, `unit_price`, `total_price`) VALUES ('$order_id', '{$item['inventory_id']}', '{$item['item_name']}', '{$item['category_name']}', '{$item['quantity']}', '{$item['unit_price']}', '{$item['total_price']}')");
        
        // Update inventory quantity
        mysqli_query($con, "UPDATE inventory SET quantity = quantity - {$item['quantity']} WHERE id = {$item['inventory_id']}");
    }

    // Clear shopping cart
    mysqli_query($con, "DELETE FROM shopping_cart WHERE patient_id = $patient_id");

    // Set session variables for success page
    $_SESSION['order_success'] = true;
    $_SESSION['order_number'] = $order_number;
    $_SESSION['order_total'] = $total_amount;
}

?>