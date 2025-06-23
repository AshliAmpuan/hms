<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order - <?php echo $row['order_number']; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> 
            <strong>Warning!</strong> This action will cancel the order and restore inventory quantities.
          </div>
          
          <div class="form-group">
            <label>Customer Name</label>
            <input type="text" class="form-control" value="<?php echo $row['firstname'].' '.$row['lastname']; ?>" readonly>
          </div>
          
          <div class="form-group">
            <label>Order Total</label>
            <input type="text" class="form-control" value="₱<?php echo number_format($row['total_amount'], 2); ?>" readonly>
            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
          </div>
          
          <div class="form-group">
            <label>Cancellation Reason <span class="text-danger">*</span></label>
            <select name="cancel_reason" class="form-control" required>
              <option value="">Select reason</option>
              <option value="Customer requested cancellation">Customer requested cancellation</option>
              <option value="Out of stock items">Out of stock items</option>
              <option value="Payment issues">Payment issues</option>
              <option value="Duplicate order">Duplicate order</option>
              <option value="System error">System error</option>
              <option value="Other">Other</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Additional Notes</label>
            <textarea name="additional_notes" class="form-control" rows="3" placeholder="Enter any additional notes (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="cancel_order" class="btn btn-danger">
            <i class="fa fa-ban"></i> Cancel Order
          </button>
        </div>
      </form>
    </div>
  </div>
</div>