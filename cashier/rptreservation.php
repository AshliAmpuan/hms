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
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/cashier-appointment.css">

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
              <div class="breadcrumb-item active"><a href="#">Entry</a></div>
              <div class="breadcrumb-item">Appointment</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Approved Appointments - Cash Payments Only</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>All Approved Reservations (Cash Only)</h4>
                    <div class="card-header-action">
                      <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus"></i> Add Laboratory</button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Contact Number</th>
                            <th>Service Type/Test</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Payment</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Modified query to show ONLY approved reservations with CASH payment method (mop = 1)
                          $query = mysqli_query($con, "SELECT 
                            reservation.id, 
                            laboratory.laboratory_name, 
                            reservation.tdate, 
                            reservation.time, 
                            laboratory.price, 
                            reservation.add_to_checkout,
                            reservation.mop,
                            patient.firstname, 
                            patient.lastname, 
                            patient.contact_number, 
                            reservation.doctor_id,
                            reservation.pet_id,
                            pet.pet_name,
                            transaction.id as transaction_id,
                            transaction.status as payment_status,
                            CASE 
                              WHEN transaction.status IS NULL AND reservation.add_to_checkout = 0 THEN 'PENDING PAYMENT'
                              WHEN transaction.status IS NULL AND reservation.add_to_checkout = 1 THEN 'PENDING PAYMENT'
                              WHEN transaction.status = 0 THEN 'PENDING PAYMENT' 
                              WHEN transaction.status = 1 THEN 'PAID' 
                              WHEN transaction.status = 2 THEN 'CANCELLED'
                              ELSE 'PENDING PAYMENT' 
                            END AS transaction_status
                            FROM reservation 
                            INNER JOIN laboratory ON laboratory.id = reservation.laboratory_id 
                            INNER JOIN patient ON patient.id = reservation.patient_id 
                            LEFT JOIN pet ON pet.id = reservation.pet_id
                            LEFT JOIN transaction ON transaction.reservation_id = reservation.id 
                            WHERE reservation.status = 1
                            AND reservation.mop = 1
                            AND (transaction.mop IS NULL OR transaction.mop = '1')
                            ORDER BY reservation.id DESC");
                            
                          $count = 0;
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['laboratory_name']; ?></td>
                            <td><?php echo $row['tdate']; ?></td>
                            <td><?php echo $row['time'] ? date('g:i A', strtotime($row['time'])) : 'Not Set'; ?></td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                              <?php 
                              // Check if reservation has no transaction record or unpaid transaction
                              if($row['transaction_id'] == null || $row['payment_status'] == 0) { ?>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#paymentModal<?php echo $row['id']; ?>">
                                  Pay Transaction
                                </button>
                              <?php } elseif($row['payment_status'] == 1) { ?>
                                <div class="badge badge-success">PAID</div>
                              <?php } elseif($row['payment_status'] == 2) { ?>
                                <div class="badge badge-danger">CANCELLED</div>
                              <?php } ?>
                            </td>
                          </tr>
                          
                          <!-- Payment Modal - Universal for all reservations -->
                          <div class="modal fade" id="paymentModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Process Payment</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form method="post">
                                  <div class="modal-body">
                                    <!-- Laboratory Name Display -->
                                    <div class="laboratory-name">
                                      <strong><?php echo $row['laboratory_name']; ?></strong>
                                    </div>
                                    <div class="patient-name">
                                      <p>Patient: <strong><?php echo $row['firstname'].' '.$row['lastname']; ?></strong></p>
                                    </div>
                                    
                                    <!-- Pet Information Display -->
                                    <?php if(!empty($row['pet_name'])) { ?>
                                    <div class="pet-name">
                                      <p>Pet: <strong><?php echo $row['pet_name']; ?></strong></p>
                                    </div>
                                    <?php } ?>
                                    
                                    <div class="form-group">
                                      <label>Amount to Pay</label>
                                      <input type="number" step="0.01" class="form-control" name="atp" id="atp<?php echo $row['id']; ?>" value="<?php echo number_format($row['price'], 2, '.', ''); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label>Amount Received</label>
                                      <input type="number" step="0.01" class="form-control" name="amount" id="amount<?php echo $row['id']; ?>" onkeyup="calculateChange(<?php echo $row['id']; ?>)" required>
                                    </div>
                                    
                                    <!-- Change Display -->
                                    <div class="change-display" id="changeDisplay<?php echo $row['id']; ?>" style="display: none;">
                                      <strong>Change: ₱<span id="changeAmount<?php echo $row['id']; ?>">0.00</span></strong>
                                    </div>
                                    
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                    <?php if($row['transaction_id'] != null) { ?>
                                      <input type="hidden" name="transaction_id" value="<?php echo $row['transaction_id']; ?>">
                                    <?php } ?>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="process_payment" class="btn btn-success">Process Payment</button>
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
      
     <?php include('../include/footer.php'); ?>
    </div>
  </div>
  
  <?php

      if(isset($_POST['submit']))
      {
          $laboratory = $_POST['laboratory'];
          $price = $_POST['price'];
          $details = $_POST['details'];
          $capacity = $_POST['capacity'];

          $laboratoryinsert = mysqli_query($con, "INSERT INTO laboratory (`laboratory_name`, `details`, `price`, `capacity_per_day`) VALUES ('$laboratory', '$details', '$price', '$capacity')");
                if($laboratoryinsert)
                {
                    echo "<script>alert('Laboratory Add Successfully!')</script>";
                    echo "<script>location.replace('laboratory.php')</script>";
                }
                else
                {
                  echo "<script>alert('Something Went Wrong!')</script>";
                }
      }
  
  ?>
  <?php

if(isset($_POST['process_payment']))
{
    date_default_timezone_set("Asia/Manila");
    $tdate = date("Y-m-d");
    
    $reservation_id = $_POST['reservation_id'];
    $atp = $_POST['atp'];
    $amount = $_POST['amount'];
    $cashier_id = $_SESSION['cashier_id'];

    if($amount >= $atp)
    {
        // Check if transaction already exists
        if(isset($_POST['transaction_id']) && !empty($_POST['transaction_id'])) {
            // Update existing transaction to paid status
            $transaction_id = $_POST['transaction_id'];
            mysqli_query($con, "UPDATE transaction SET `status` = 1, `mop` = '1' WHERE id = $transaction_id");
        } else {
            // Create new transaction record with paid status and cash payment method
            mysqli_query($con, "INSERT INTO transaction (`reservation_id`, `price`, `tdate`, `cashier_id`, `status`, `mop`) VALUES ('$reservation_id', '$atp', '$tdate', '$cashier_id', 1, '1')");
        }
        
        // Update reservation with paid_by instead of approve_by
        mysqli_query($con, "UPDATE reservation SET `status` = 1, `paid_by` = $cashier_id WHERE id = $reservation_id");
        
        echo "<script>alert('Payment processed successfully!');</script>";
        echo "<script>window.location.replace('" . $_SERVER['PHP_SELF'] . "');</script>";
    }
    else
    {
        echo "<script>alert('Insufficient Amount!');</script>";
    }
}

if(isset($_POST['approve']))
{
    date_default_timezone_set("Asia/Manila");
    $tdate = date("Y-m-d");            

    $id = $_POST['id'];
    $cashier_id = $_SESSION['cashier_id'];
    $atp = $_POST['atp'];
    $amount = $_POST['amount'];

    if($amount >= $atp)
    {
        // Update reservation with paid_by instead of approve_by
        mysqli_query($con, "UPDATE reservation SET `status` = 1, `paid_by` = $cashier_id WHERE id = $id");

        mysqli_query($con, "INSERT INTO transaction (`reservation_id`, `price`, `tdate`, `cashier_id`, `status`, `mop`) VALUES ('$id', '$atp', '$tdate', '$cashier_id', 1, '1')");

        echo "<script>window.location.replace('rptreservation.php')</script>";
    }
    else
    {
        echo "<script>alert('Insufficient Amount!')</script>";
    }

}

if(isset($_POST['cancel']))
{
    $id = $_POST['id'];
    mysqli_query($con, "UPDATE transaction SET `status` = 2 WHERE reservation_id = $id");
    mysqli_query($con, "UPDATE reservation SET `status` = 2 WHERE id = $id");
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
  
  <!-- JS Libraies -->
  <script src="../assets/modules/datatables/datatables.min.js"></script>
  <script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
  <script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/js/page/modules-datatables.js"></script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
  
  <!-- Custom JS -->
  <script src="../assets/js/cashier-rptreservation.js"></script>
</body>
</html>