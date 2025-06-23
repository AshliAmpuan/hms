<!-- Approve Payment Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="exampleModalsuccess<?php echo $row['id']; ?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <p>Are you sure you want to confirm payment for Order #<?php echo $row['order_number']; ?>?</p>
          <div class="row">
            <div class="col-md-6">
              <strong>Patient:</strong> <?php echo $row['firstname'].' '.$row['lastname']; ?>
            </div>
            <div class="col-md-6">
              <strong>Total Amount:</strong> ₱<?php echo number_format($row['total_amount'], 2); ?>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-6">
              <strong>Total Items:</strong> <?php echo $row['total_items']; ?>
            </div>
            <div class="col-md-6">
              <strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($row['order_date'])); ?>
            </div>
          </div>
          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        </div>
        <div class="modal-footer bg-whitesmoke br">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="approve_payment" class="btn btn-success">Confirm Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>