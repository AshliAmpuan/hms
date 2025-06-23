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
              <div class="breadcrumb-item">Approve Appointment</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Approve Appointment</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Approve Appointment Table</h4>
                    <div class="card-header-action">
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
                            <th>Reference</th>
                            <th>Contact Number</th>
                            <th>Service Type/Test</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // $id = $_SESSION['patient_id'];
                            $query = mysqli_query($con, "SELECT reservation.id, reservation.reference, laboratory.laboratory_name, reservation.tdate, laboratory.price, 
                            (SELECT CASE WHEN reservation.status = 0 THEN 'PENDING' WHEN reservation.status = 1 THEN 'APPROVED' ELSE 'CANCELLED' END) 
                            AS reservation_status, reservation.status, patient.firstname, patient.lastname, patient.contact_number FROM reservation INNER JOIN laboratory ON laboratory.id=reservation.laboratory_id 
                            INNER JOIN patient ON patient.id=reservation.patient_id WHERE reservation.add_to_checkout = 1 AND reservation.status = 1 and reservation.mop = 2");
                             $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['reference']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['laboratory_name']; ?></td>
                            <td><?php echo $row['tdate']; ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td>
                              <?php if($row['status'] == 0) { ?>
                                <buttton class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModalsuccess<?php echo $row['id']; ?>"><i class="fa fa-check"></i> Approve</buttton> 
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModaldanger<?php echo $row['id']; ?>"><i class="fa fa-ban"></i> Cancel</a>
                                <?php } else {  ?>
                                  <div class="badge badge-<?php if($row['status'] == 0) { echo 'warning'; } else if($row['status'] == 1) { echo 'success'; } else { echo 'danger'; } ?>">
                                    <?php echo $row['reservation_status']; ?>
                                </div>
                                  <?php } ?>
                            </td>
                          </tr>
                          <?php
                        include 'modal/online-approve.php';
                        include 'modal/online-decline.php';
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

if(isset($_POST['approve']))
{
    date_default_timezone_set("Asia/Manila");
    $tdate = date("Y-m-d");            

    $id = $_POST['id'];
    $cashier_id = $_SESSION['cashier_id'];
   
        mysqli_query($con, "UPDATE reservation SET `status` = 1, `approve_by` = $cashier_id WHERE id = $id");
        mysqli_query($con, "UPDATE transaction SET `status` = 1 WHERE reservation_id = $id");
       
        echo "<script>window.location.replace('online-payer.php')</script>";
   

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
</body>
</html>