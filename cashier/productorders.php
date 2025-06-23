<?php include('../include/cashier_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <?php include('../include/title.php'); ?>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  <!-- Custom Orders CSS -->
  <link rel="stylesheet" href="../assets/css/cashier-orders.css">

  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-94034622-3');
  </script>
  <!-- /END GA -->
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include('../include/header.php'); ?>
      <?php include('../include/sidebar.php'); ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1></h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Management</a></div>
              <div class="breadcrumb-item">Orders</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Orders Management</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Orders Table</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Contact Number</th>
                            <th>Order Number</th>
                            <th>Order Items</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = mysqli_query($con, "SELECT 
                                orders.id, 
                                orders.order_number, 
                                orders.total_amount, 
                                orders.total_items,
                                orders.order_status,
                                orders.payment_status,
                                orders.order_date,
                                orders.mop,
                                patient.firstname, 
                                patient.lastname, 
                                patient.contact_number
                            FROM orders 
                            INNER JOIN patient ON patient.id = orders.patient_id 
                            WHERE orders.payment_status = 'unpaid' 
                            ORDER BY orders.order_date DESC");
                            
                            $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <?php
                            // Get order items for this order
                            $items_query = mysqli_query($con, "SELECT item_name, quantity FROM order_items WHERE order_id = {$row['id']} ORDER BY item_name");
                            $items_list = array();
                            while($item = mysqli_fetch_array($items_query)) {
                              $items_list[] = $item['item_name'] . ' (x' . $item['quantity'] . ')';
                            }
                            $items_display = !empty($items_list) ? implode(', ', $items_list) : 'No items';
                            
                            // Truncate if too long
                            if(strlen($items_display) > 50) {
                              $items_display = substr($items_display, 0, 47) . '...';
                            }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['order_number']; ?></td>
                            <td>
                              <span title="<?php echo implode(', ', $items_list); ?>" data-toggle="tooltip">
                                <?php echo $items_display; ?>
                              </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($row['order_date'])); ?></td>
                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                              <?php if($row['order_status'] == 'pending' && $row['payment_status'] == 'unpaid') { ?>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModalsuccess<?php echo $row['id']; ?>">
                                  <i class="fa fa-check"></i> Approve
                                </button> 
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModaldanger<?php echo $row['id']; ?>">
                                  <i class="fa fa-ban"></i> Cancel
                                </a>
                              <?php } else { ?>
                                <div class="badge badge-<?php if($row['order_status'] == 'pending') { echo 'warning'; } else if($row['order_status'] == 'completed') { echo 'success'; } else { echo 'danger'; } ?>">
                                  <?php echo strtoupper($row['order_status']); ?>
                                </div>
                              <?php } ?>
                            </td>
                          </tr>

                          <!-- Approval Modal -->
                          <div class="modal fade" id="exampleModalsuccess<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Approve Order</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form method="post">
                                  <div class="modal-body">
                                    <!-- Order Info Display -->
                                    <div class="order-info">
                                      <strong>Order #<?php echo $row['order_number']; ?></strong><br>
                                      <small><?php echo $row['firstname'].' '.$row['lastname']; ?> - <?php echo $row['total_items']; ?> items</small>
                                      <br>
                                      <?php
                                      // Get detailed order items for modal
                                      $modal_items_query = mysqli_query($con, "SELECT item_name, quantity, unit_price FROM order_items WHERE order_id = {$row['id']} ORDER BY item_name");
                                      $modal_items = array();
                                      while($modal_item = mysqli_fetch_array($modal_items_query)) {
                                        $modal_items[] = $modal_item['item_name'] . ' (x' . $modal_item['quantity'] . ') - ₱' . number_format($modal_item['unit_price'], 2);
                                      }
                                      ?>
                                      <small class="text-muted">Items: <?php echo implode(', ', $modal_items); ?></small>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label>Amount to Pay</label>
                                      <input type="number" class="form-control" name="atp" id="atp<?php echo $row['id']; ?>" value="<?php echo number_format($row['total_amount'], 2); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label>Amount Received</label>
                                      <input type="number" step="0.01" class="form-control" name="amount" id="amount<?php echo $row['id']; ?>" onkeyup="calculateChange(<?php echo $row['id']; ?>)" required>
                                    </div>
                                    
                                    <!-- Change Display -->
                                    <div class="change-display" id="changeDisplay<?php echo $row['id']; ?>" style="display: none;">
                                      <strong>Change: ₱<span id="changeAmount<?php echo $row['id']; ?>">0.00</span></strong>
                                    </div>
                                    
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="total_amount" value="<?php echo $row['total_amount']; ?>">
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="accept_payment" class="btn btn-success">Approve</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                          <!-- Cancel Modal -->
                          <div class="modal fade" id="exampleModaldanger<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Cancel Order</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form method="post">
                                  <div class="modal-body">
                                    <p>Are you sure you want to cancel this order for <strong><?php echo $row['firstname'].' '.$row['lastname']; ?></strong>?</p>
                                    <div class="order-info">
                                      <strong>Order #<?php echo $row['order_number']; ?></strong><br>
                                      <small>Total Amount: ₱<?php echo number_format($row['total_amount'], 2); ?></small>
                                      <br>
                                      <?php
                                      // Get order items for cancel modal  
                                      $cancel_items_query = mysqli_query($con, "SELECT item_name, quantity FROM order_items WHERE order_id = {$row['id']} ORDER BY item_name");
                                      $cancel_items = array();
                                      while($cancel_item = mysqli_fetch_array($cancel_items_query)) {
                                        $cancel_items[] = $cancel_item['item_name'] . ' (x' . $cancel_item['quantity'] . ')';
                                      }
                                      ?>
                                      <small class="text-muted">Items: <?php echo implode(', ', $cancel_items); ?></small>
                                    </div>
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="decline_order" class="btn btn-danger">Cancel Order</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Order Details Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="orderDetailsModal">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Order Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
              <!-- Content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      
      <?php include('../include/footer.php'); ?>
    </div>
  </div>

  <?php
  // Accept Payment - Process immediately as cash payment
  if(isset($_POST['accept_payment']))
  {
      date_default_timezone_set("Asia/Manila");
      $payment_date = date("Y-m-d H:i:s");
      $tdate = date("Y-m-d");

      $order_id = $_POST['order_id'];
      $cashier_id = $_SESSION['cashier_id'];
      $total_amount = $_POST['total_amount'];
      $atp = $_POST['atp'];
      $amount = $_POST['amount'];

      if($amount >= $atp)
      {
          // Update order status and payment (Cash payment processed immediately)
          mysqli_query($con, "UPDATE orders SET 
              `order_status` = 'completed', 
              `payment_status` = 'paid',
              `mop` = 'Cash',
              `completed_date` = '$payment_date',
              `updated_at` = '$payment_date'
              WHERE id = $order_id");

          // Insert transaction record (Full cash payment)
          mysqli_query($con, "INSERT INTO transaction (`reservation_id`, `price`, `tdate`, `cashier_id`) 
              VALUES ('$order_id', '$atp', '$tdate', '$cashier_id')");

          echo "<script>window.location.replace('productorders.php')</script>";
      }
      else
      {
          echo "<script>alert('Insufficient Amount!')</script>";
      }
  }

  // Decline Order
  if(isset($_POST['decline_order']))
  {
      date_default_timezone_set("Asia/Manila");
      $decline_date = date("Y-m-d H:i:s");

      $order_id = $_POST['order_id'];
      $cancel_reason = 'Order cancelled by cashier';

      mysqli_query($con, "UPDATE orders SET 
          `order_status` = 'cancelled',
          `payment_status` = 'cancelled',
          `notes` = '$cancel_reason',
          `updated_at` = '$decline_date'
          WHERE id = $order_id");

      // Restore inventory quantities
      $order_items_query = mysqli_query($con, "SELECT inventory_id, quantity FROM order_items WHERE order_id = $order_id");
      while($item = mysqli_fetch_array($order_items_query)) {
          mysqli_query($con, "UPDATE inventory SET quantity = quantity + {$item['quantity']} WHERE id = {$item['inventory_id']}");
      }

      echo "<script>window.location.reload()</script>";
  }
  ?>

  <!-- General JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  
  <!-- JS Libraries -->
  <script src="../assets/modules/datatables/datatables.min.js"></script>
  <script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
  <script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/js/page/modules-datatables.js"></script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
  
  <!-- Custom Orders JS -->
  <script src="../assets/js/cashier-orders.js"></script>
</body>
</html>