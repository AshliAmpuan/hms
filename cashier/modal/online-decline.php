<div class="modal fade" tabindex="-1" role="dialog" id="exampleModaldanger<?php echo $row['id']; ?>">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div style="color: red; text-align: center;">
                  <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <i class="fa fa-check-circle fa-4x"></i>
                  <p>Are you sure you want to decline this reservation?</p>
                </div>
              </div>
              <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="decline" class="btn btn-danger">Yes</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <?php

          if(isset($_POST['decline']))
          {
              $id = $_POST['id'];
              $cashier_id = $_SESSION['cashier_id'];

              mysqli_query($con, "UPDATE reservation SET `status` = 2, `approve_by` = $cashier_id WHERE id = $id");

              mysqli_query($con, "UPDATE transaction SET `status` = 2, WHERE reservation_id = $id");

              echo "<script>window.location.replace('rptreservation.php')</script>";
          }
        
        ?>