<div class="modal fade" tabindex="-1" role="dialog" id="exampleModalsuccess<?php echo $row['id']; ?>">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                  <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="doctor_id" value="<?php echo $row['doctor_id']; ?>">
                    <input type="hidden" name="atp" value="<?php echo $row['price']; ?>">
                    <div class="col-lg-12">
                        <div class="form-group">
                          <label>Amount to be paid</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-money-bill"></i>
                              </div>
                            </div>
                            <input type="text" disabled value="<?php echo number_format($row['price'], 2); ?>" class="form-control" placeholder="Laboratory" name="amout_to_be_paid">
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Amount</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-money-bill"></i>
                              </div>
                            </div>
                            <input type="number" class="form-control" required placeholder="Amount" name="amount">
                          </div>
                        </div>
                      </div>
              </div>
              <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="approve" class="btn btn-success">Yes</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        