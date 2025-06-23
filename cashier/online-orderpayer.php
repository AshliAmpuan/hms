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
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<style>
  .modal-backdrop {
  z-index: 0;
}
</style>
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
            <h1></h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Entry</a></div>
              <div class="breadcrumb-item">Online Orders</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Online Orders</h2>
            <!-- <p class="section-lead">
              We use 'DataTables' made by @SpryMedia. You can check the full documentation <a href="https://datatables.net/">here</a>.
            </p> -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Online Orders Table</h4>
                    <div class="card-header-action">
                      <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus"></i> Add Order</button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                          <th>
                              #
                            </th>
                            <th>Patient</th>
                            <th>Order Number</th>
                            <th>Contact Number</th>
                            <th>Total Items</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Query to get orders with payment method = 2 (assuming online payment)
                            $query = mysqli_query($con, "SELECT orders.id, orders.order_number, orders.total_amount, orders.total_items, 
                            orders.order_date, orders.payment_status, orders.order_status, patient.firstname, patient.lastname, patient.contact_number 
                            FROM orders 
                            INNER JOIN patient ON patient.id = orders.patient_id 
                            WHERE orders.mop = '2' AND orders.order_status = 'pending'");
                             $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['order_number']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['total_items']; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($row['order_date'])); ?></td>
                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                              <div class="badge badge-<?php 
                                if($row['payment_status'] == 'unpaid') { echo 'warning'; } 
                                else if($row['payment_status'] == 'paid') { echo 'success'; } 
                                else if($row['payment_status'] == 'partial') { echo 'info'; } 
                                else { echo 'danger'; } 
                              ?>">
                                <?php echo strtoupper($row['payment_status']); ?>
                              </div>
                            </td>
                            <td>
                              <?php if($row['payment_status'] == 'unpaid') { ?>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModalsuccess<?php echo $row['id']; ?>"><i class="fa fa-check"></i> Confirm Payment</button> 
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModaldanger<?php echo $row['id']; ?>"><i class="fa fa-ban"></i> Cancel</a>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#orderDetailsModal<?php echo $row['id']; ?>"><i class="fa fa-eye"></i> View Items</button>
                                <?php } else {  ?>
                                  <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#orderDetailsModal<?php echo $row['id']; ?>"><i class="fa fa-eye"></i> View Items</button>
                                  <?php } ?>
                            </td>
                          </tr>
                          <?php
                        include 'modal/online-order-approve.php';
                        include 'modal/cancel_order.php';
                        include 'modal/order_details.php';
                        } ?>
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
      
     <?php include('../include/footer.php'); ?>
    </div>
  </div>
  
  <?php
  // Handle order approval/payment confirmation
  if(isset($_POST['approve_payment']))
  {
      date_default_timezone_set("Asia/Manila");
      $completed_date = date("Y-m-d H:i:s");            

      $id = $_POST['id'];
      $cashier_id = $_SESSION['cashier_id'];
     
          mysqli_query($con, "UPDATE orders SET `payment_status` = 'paid', `order_status` = 'completed', `completed_date` = '$completed_date', `created_by` = $cashier_id WHERE id = $id");
         
          echo "<script>window.location.replace('online-cart-payer.php')</script>";
  }

  // Handle order cancellation
  if(isset($_POST['cancel_order']))
  {
      $id = $_POST['id'];
      $notes = $_POST['cancel_reason'];
      $cashier_id = $_SESSION['cashier_id'];
     
          mysqli_query($con, "UPDATE orders SET `order_status` = 'cancelled', `payment_status` = 'refunded', `notes` = '$notes', `created_by` = $cashier_id WHERE id = $id");
         
          echo "<script>window.location.replace('online-cart-payer.php')</script>";
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
  
  <!-- JS Libraies -->
  <script src="../assets/modules/datatables/datatables.min.js"></script>
  <script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
  <script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/modules/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#table-1').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
      });
    });
  </script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>