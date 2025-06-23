<?php 
include('../include/cashier_session.php');

if(isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    // Get order items using the same structure as your modal
    $items_query = mysqli_query($con, "SELECT * FROM order_items WHERE order_id = $order_id");
    
    if(mysqli_num_rows($items_query) > 0) {
        $item_count = 0;
        $grand_total = 0;
?>
        <div class="order-items">
            <?php while($item = mysqli_fetch_array($items_query)) { 
                $item_count++;
                $grand_total += $item['total_price'];
            ?>
            <div class="item-row">
                <div class="item-info">
                    <strong><?php echo $item['item_name']; ?></strong>
                    <small><?php echo $item['category_name']; ?></small>
                </div>
                <div class="item-details">
                    Qty: <?php echo $item['quantity']; ?> × ₱<?php echo number_format($item['unit_price'], 2); ?>
                    = <strong>₱<?php echo number_format($item['total_price'], 2); ?></strong>
                </div>
            </div>
            <?php } ?>
            
            <div class="total-row">
                <strong>Total: ₱<?php echo number_format($grand_total, 2); ?></strong>
            </div>
        </div>

        <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/cashier-order-detail.css">
<?php 
    } else { 
?>
        <div class="alert alert-warning">
            No items found for this order.
        </div>
<?php 
    }
} else {
?>
    <div class="alert alert-danger">
        Invalid request.
    </div>
<?php
}
?>