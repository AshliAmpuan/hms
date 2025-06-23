<!-- View Order Details Modal -->
<div class="modal fade" id="viewOrderModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewOrderModalLabel">Order Details - <?php echo $row['order_number']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6><strong>Customer Information</strong></h6>
            <p><strong>Name:</strong> <?php echo $row['firstname'].' '.$row['lastname']; ?></p>
            <p><strong>Contact:</strong> <?php echo $row['contact_number']; ?></p>
          </div>
          <div class="col-md-6">
            <h6><strong>Order Information</strong></h6>
            <p><strong>Order Number:</strong> <?php echo $row['order_number']; ?></p>
            <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></p>
            <p><strong>Total Items:</strong> <?php echo $row['total_items']; ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($row['total_amount'], 2); ?></p>
          </div>
        </div>
        
        <hr>
        
        <h6><strong>Order Items</strong></h6>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $items_query = mysqli_query($con, "SELECT * FROM order_items WHERE order_id = {$row['id']}");
              while($item = mysqli_fetch_array($items_query)) {
              ?>
              <tr>
                <td><?php echo $item['item_name']; ?></td>
                <td><?php echo $item['category_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['unit_price'], 2); ?></td>
                <td>₱<?php echo number_format($item['total_price'], 2); ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>