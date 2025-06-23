<?php 
include('../include/patient_session.php');

if(isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $patient_id = $_SESSION['patient_id'];
    
    // Get order details
    $order_query = mysqli_query($con, "SELECT 
        o.id, 
        o.order_number, 
        o.order_date, 
        o.completed_date,
        o.total_items, 
        o.total_amount, 
        o.order_status, 
        o.payment_status,
        o.mop,
        o.notes
      FROM orders o 
      WHERE o.id = $order_id AND o.patient_id = $patient_id");
    
    if(mysqli_num_rows($order_query) > 0) {
        $order = mysqli_fetch_array($order_query);
        
        // Get order items
        $items_query = mysqli_query($con, "SELECT 
            oi.item_name,
            oi.category_name,
            oi.quantity,
            oi.unit_price,
            oi.total_price,
            oi.item_details
          FROM order_items oi 
          WHERE oi.order_id = $order_id
          ORDER BY oi.item_name");
?>

<div class="row">
  <div class="col-md-6">
    <h6><strong>Order Information</strong></h6>
    <table class="table table-borderless table-sm">
      <tr>
        <td><strong>Order Number:</strong></td>
        <td><?php echo $order['order_number']; ?></td>
      </tr>
      <tr>
        <td><strong>Order Date:</strong></td>
        <td><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></td>
      </tr>
      <?php if($order['completed_date']) { ?>
      <tr>
        <td><strong>Completed Date:</strong></td>
        <td><?php echo date('M d, Y h:i A', strtotime($order['completed_date'])); ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td><strong>Total Items:</strong></td>
        <td><?php echo $order['total_items']; ?></td>
      </tr>
      <tr>
        <td><strong>Total Amount:</strong></td>
        <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
      </tr>
    </table>
  </div>
  
  <div class="col-md-6">
    <h6><strong>Status & Payment</strong></h6>
    <table class="table table-borderless table-sm">
      <tr>
        <td><strong>Order Status:</strong></td>
        <td>
          <?php 
          $status_class = '';
          switch($order['order_status']) {
            case 'pending': $status_class = 'warning'; break;
            case 'completed': $status_class = 'success'; break;
            case 'cancelled': $status_class = 'danger'; break;
            case 'refunded': $status_class = 'info'; break;
          }
          ?>
          <span class="badge badge-<?php echo $status_class; ?>">
            <?php echo strtoupper($order['order_status']); ?>
          </span>
        </td>
      </tr>
      <tr>
        <td><strong>Payment Status:</strong></td>
        <td>
          <?php 
          $payment_class = '';
          switch($order['payment_status']) {
            case 'paid': $payment_class = 'success'; break;
            case 'unpaid': $payment_class = 'danger'; break;
            case 'partial': $payment_class = 'warning'; break;
            case 'refunded': $payment_class = 'info'; break;
          }
          ?>
          <span class="badge badge-<?php echo $payment_class; ?>">
            <?php echo strtoupper($order['payment_status']); ?>
          </span>
        </td>
      </tr>
      <tr>
        <td><strong>Payment Method:</strong></td>
        <td><?php echo $order['mop'] ? $order['mop'] : 'N/A'; ?></td>
      </tr>
      <?php if($order['notes']) { ?>
      <tr>
        <td><strong>Notes:</strong></td>
        <td><?php echo nl2br(htmlspecialchars($order['notes'])); ?></td>
      </tr>
      <?php } ?>
    </table>
  </div>
</div>

<hr>

<h6><strong>Order Items</strong></h6>
<div class="table-responsive">
  <table class="table table-striped table-sm">
    <thead>
      <tr>
        <th>Item</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Total</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $subtotal = 0;
      while($item = mysqli_fetch_array($items_query)) { 
        $subtotal += $item['total_price'];
      ?>
      <tr>
        <td><strong><?php echo $item['item_name']; ?></strong></td>
        <td><?php echo $item['category_name']; ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>₱<?php echo number_format($item['unit_price'], 2); ?></td>
        <td><strong>₱<?php echo number_format($item['total_price'], 2); ?></strong></td>
        <td><?php echo $item['item_details'] ? $item['item_details'] : 'N/A'; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr class="table-active">
        <th colspan="4" class="text-right">Subtotal:</th>
        <th><strong>₱<?php echo number_format($subtotal, 2); ?></strong></th>
        <th></th>
      </tr>
      <tr class="table-active">
        <th colspan="4" class="text-right">Total Amount:</th>
        <th><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>

<?php
    } else {
        echo '<div class="alert alert-danger">Order not found or access denied.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>