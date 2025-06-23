<?php include('../include/patient_session.php'); ?>
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
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

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
            <h1>Order History</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Orders</a></div>
              <div class="breadcrumb-item">Order History</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">My Orders</h2>
            <p class="section-lead">
              View your complete order history and track your purchases.
            </p>
            
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Order History</h4>
                    <div class="card-header-action">
                      <a href="petshop.php" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Continue Shopping</a>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Order Status</th>
                            <th>Payment Method</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $id = $_SESSION['patient_id'];
                          $query = mysqli_query($con, "SELECT 
                              o.id, 
                              o.order_number, 
                              o.order_date, 
                              o.total_items, 
                              o.total_amount, 
                              o.order_status, 
                              o.mop,
                              o.completed_date,
                              o.notes
                            FROM orders o 
                            WHERE o.patient_id = $id 
                            ORDER BY o.order_date DESC");
                          
                          $count = 0;
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                            
                            // Format date
                            $order_date = date('M d, Y h:i A', strtotime($row['order_date']));
                            
                            // Status colors
                            $order_status_class = '';
                            switch($row['order_status']) {
                              case 'pending': $order_status_class = 'warning'; break;
                              case 'completed': $order_status_class = 'success'; break;
                              case 'cancelled': $order_status_class = 'danger'; break;
                              case 'refunded': $order_status_class = 'info'; break;
                              default: $order_status_class = 'secondary';
                            }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td>
                              <strong><?php echo $row['order_number']; ?></strong>
                            </td>
                            <td><?php echo $order_date; ?></td>
                            <td>
                              <span class="badge badge-primary"><?php echo $row['total_items']; ?> items</span>
                            </td>
                            <td>
                              <strong>₱<?php echo number_format($row['total_amount'], 2); ?></strong>
                            </td>
                            <td>
                              <span class="badge badge-<?php echo $order_status_class; ?>">
                                <?php echo strtoupper($row['order_status']); ?>
                              </span>
                            </td>
                            <td>
                              <?php echo $row['mop'] ? $row['mop'] : 'N/A'; ?>
                            </td>
                            <td>
                              <button class="btn btn-info btn-sm" onclick="viewOrderDetails(<?php echo $row['id']; ?>)">
                                <i class="fas fa-eye"></i> View Details
                              </button>
                              <?php if($row['order_status'] == 'completed') { ?>
                                <button class="btn btn-success btn-sm" onclick="downloadReceipt(<?php echo $row['id']; ?>)">
                                  <i class="fas fa-download"></i> Receipt
                                </button>
                              <?php } ?>
                            </td>
                          </tr>
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

  <script>
    function viewOrderDetails(orderId) {
      $.ajax({
        url: 'get_order_details.php',
        method: 'POST',
        data: { order_id: orderId },
        success: function(response) {
          $('#orderDetailsContent').html(response);
          $('#orderDetailsModal').modal('show');
        },
        error: function() {
          alert('Error loading order details');
        }
      });
    }

    function downloadReceipt(orderId) {
      window.open('download_receipt.php?order_id=' + orderId, '_blank');
    }

    // Initialize DataTable with custom settings
$(document).ready(function() {
  if (!$.fn.DataTable.isDataTable('#table-1')) {
    $('#table-1').DataTable({
      "order": [[ 2, "desc" ]], // Sort by date column (index 2) descending
      "pageLength": 25,
      "responsive": true,
      "columnDefs": [
        { "orderable": false, "targets": [7] } // Disable sorting on Actions column
      ]
    });
  }
});

  </script>
</body>
</html>
