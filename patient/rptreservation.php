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

  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
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
            <h2 class="section-title">My Appointments</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Appointment History</h4>
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
                            <th>Service Type/Test</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Veterinarian</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $id = $_SESSION['patient_id'];
                          $query = mysqli_query($con, "SELECT 
                              reservation.id, 
                              laboratory.laboratory_name, 
                              category.category as category_name, 
                              reservation.tdate, 
                              reservation.time, 
                              laboratory.price,   
                              reservation.status as reservation_status,
                              reservation.doctor_id,
                              doctor.fullname as doctor_fullname,
                              COALESCE(transaction.status, 0) as transaction_status,
                              (SELECT CASE 
                                  WHEN reservation.status = 0 THEN 'No Veterinarian Assigned'
                                  WHEN reservation.status = 1 AND reservation.doctor_id IS NOT NULL THEN doctor.fullname
                                  WHEN reservation.status = 1 AND reservation.doctor_id IS NULL THEN 'Approved'
                                  WHEN reservation.status = 2 THEN 'CANCELLED'
                                  ELSE 'UNKNOWN' 
                              END) AS reservation_status_text,
                              (SELECT CASE 
                                  WHEN reservation.status = 2 THEN 'CANCELLED'
                                  WHEN COALESCE(transaction.status, 0) = 0 THEN 'NOT PAID' 
                                  WHEN transaction.status = 1 THEN 'PAID' 
                                  ELSE 'CANCELLED' 
                              END) AS payment_status
                          FROM reservation 
                          INNER JOIN laboratory ON laboratory.id = reservation.laboratory_id 
                          INNER JOIN patient ON patient.id = reservation.patient_id 
                          LEFT JOIN category ON category.id = laboratory.category_id 
                          LEFT JOIN transaction ON transaction.reservation_id = reservation.id
                          LEFT JOIN doctor ON doctor.id = reservation.doctor_id
                          WHERE reservation.patient_id = $id 
                          ORDER BY reservation.tdate DESC, 
                                   CASE WHEN reservation.status = 2 THEN 1 ELSE 0 END ASC,
                                   reservation.status ASC");
                          
                          $count = 0;
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                          ?>
                          <tr <?php if($row['reservation_status'] == 2) { echo 'style="opacity: 0.6; background-color: #f8f9fa;"'; } ?>>
                            <td><?php echo $count; ?></td>
                            <td>
                              <?php 
                                echo $row['laboratory_name']; 
                                if(!empty($row['category_name'])) {
                                  echo ' <span style="color: #6c757d;">(' . $row['category_name'] . ')</span>';
                                }
                              ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['tdate'])); ?></td>
                            <td>
                              <?php 
                                if(!empty($row['time']) && $row['time'] != '00:00:00') {
                                  echo date('h:i A', strtotime($row['time'])); 
                                } else {
                                  echo '<span style="color: #6c757d; font-style: italic;">Not set</span>';
                                }
                              ?>
                            </td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                              <?php 
                                // Display doctor name without badge styling
                                if($row['reservation_status'] == 0) { 
                                  echo '<span style="color: #6c757d; font-style: italic;">No Veterinarian Assigned</span>';
                                } else if($row['reservation_status'] == 1) { 
                                  if(!empty($row['doctor_fullname'])) {
                                    echo $row['doctor_fullname'];
                                  } else {
                                    echo '<span style="color: #28a745;">Approved</span>';
                                  }
                                } else if($row['reservation_status'] == 2) { 
                                  // For cancelled reservations, show doctor name if available
                                  if(!empty($row['doctor_fullname'])) {
                                    echo '<span style="color: #6c757d;">' . $row['doctor_fullname'] . '</span>';
                                  } else {
                                    echo '<span style="color: #6c757d; font-style: italic;">No Veterinarian Assigned</span>';
                                  }
                                } else { 
                                  echo '<span style="color: #6c757d;">UNKNOWN</span>';
                                }
                              ?>
                            </td>
                            <td>
                              <span class="badge badge-<?php 
                                if($row['reservation_status'] == 2) { 
                                  echo 'danger'; 
                                } else if($row['transaction_status'] == 0) { 
                                  echo 'warning'; 
                                } else if($row['transaction_status'] == 1) { 
                                  echo 'success'; 
                                } else { 
                                  echo 'danger'; 
                                } 
                              ?>">
                                <?php echo $row['payment_status']; ?>
                              </span>
                            </td>
                            <td>
                              <?php if($row['reservation_status'] == 1 && $row['transaction_status'] == 1) { ?>
                                <a href="viewfiles.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                  <i class="fas fa-eye"></i> View Files
                                </a>
                              <?php } else if($row['reservation_status'] == 2) { ?>
                                <span class="text-muted">
                                  <i class="fas fa-ban"></i> Cancelled
                                </span>
                              <?php } else if($row['reservation_status'] == 0 || ($row['reservation_status'] == 1 && $row['transaction_status'] == 0)) { ?>
                                <button class="btn btn-danger btn-sm" onclick="cancelReservation(<?php echo $row['id']; ?>)">
                                  <i class="fas fa-times"></i> Cancel Booking
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
      
      <!-- Add Laboratory Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Laboratory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="POST" onsubmit="return validateLaboratoryForm()">
                  <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Service Type/Test</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-flask"></i>
                              </div>
                            </div>
                            <input type="text" class="form-control" placeholder="Laboratory" name="laboratory" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Category</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-tags"></i>
                              </div>
                            </div>
                            <select class="form-control" name="category_id" required>
                              <option value="">Select Category</option>
                              <?php
                              $category_query = mysqli_query($con, "SELECT id, category FROM category WHERE active = 1 ORDER BY category");
                              while($category_row = mysqli_fetch_array($category_query)) {
                                echo '<option value="' . $category_row['id'] . '">' . $category_row['category'] . '</option>';
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Price</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-money-bill"></i>
                              </div>
                            </div>
                            <input type="number" step="0.01" class="form-control" placeholder="Price" name="price" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Capacity Per Day</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-users"></i>
                              </div>
                            </div>
                            <input type="number" class="form-control" placeholder="Capacity" name="capacity" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Details</label>
                          <div class="input-group">
                            <textarea name="details" class="form-control" rows="4" placeholder="Enter laboratory details..."></textarea>
                          </div>
                        </div>
                      </div>
                  </div>
                  
              </div>
              <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
              </div>
              </form>
            </div>
          </div>
        </div>
     
     <?php include('../include/footer.php'); ?>
    </div>
  </div>

  <?php
  // Handle laboratory addition
  if(isset($_POST['submit'])) {
      $laboratory = mysqli_real_escape_string($con, $_POST['laboratory']);
      $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
      $price = mysqli_real_escape_string($con, $_POST['price']);
      $details = mysqli_real_escape_string($con, $_POST['details']);
      $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
      $clinic_id = $_SESSION['clinic_id'] ?? null; // Assuming clinic_id is stored in session

      $laboratoryinsert = mysqli_query($con, "INSERT INTO laboratory (`clinic_id`, `category_id`, `laboratory_name`, `details`, `price`, `capacity_per_day`) VALUES ('$clinic_id', '$category_id', '$laboratory', '$details', '$price', '$capacity')");
      if($laboratoryinsert) {
          echo "<script>alert('Laboratory Added Successfully!')</script>";
          echo "<script>location.replace('laboratory.php')</script>";
      } else {
        echo "<script>alert('Something Went Wrong!')</script>";
      }
  }

  // Handle reservation cancellation
  if(isset($_POST['cancel_reservation'])) {
      $reservation_id = mysqli_real_escape_string($con, $_POST['reservation_id']);
      $patient_id = $_SESSION['patient_id'];
      
      // Verify the reservation belongs to the current patient and can be cancelled
      $verify_query = mysqli_query($con, "SELECT id, status FROM reservation WHERE id = '$reservation_id' AND patient_id = '$patient_id' AND status IN (0, 1)");
      
      if(mysqli_num_rows($verify_query) > 0) {
          $row = mysqli_fetch_array($verify_query);
          
          // Check if reservation can be cancelled
          if($row['status'] == 0 || $row['status'] == 1) {
              // Update reservation status to cancelled (2) instead of deleting
              $cancel_query = mysqli_query($con, "UPDATE reservation SET status = 2 WHERE id = '$reservation_id' AND patient_id = '$patient_id'");
              
              if($cancel_query) {
                  echo "<script>
                      alert('Reservation cancelled successfully!');
                      window.location.href = window.location.href.split('?')[0];
                  </script>";
              } else {
                  echo "<script>alert('Error cancelling reservation!')</script>";
              }
          } else {
              echo "<script>alert('This reservation cannot be cancelled!')</script>";
          }
      } else {
          echo "<script>alert('Reservation not found or already cancelled!')</script>";
      }
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
  
  <!-- Page Specific JavaScript -->
  <script src="../assets/js/patient-rptreservation.js"></script>
</body>
</html>