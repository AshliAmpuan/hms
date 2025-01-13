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
                    <h3 style="text-align: center">Do you want to approve this transaction?</h3>
                  <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              </div>
              <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="approve" class="btn btn-success">Yes</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        