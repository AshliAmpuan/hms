<?php include('../include/admin_session.php'); ?>
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
  
  <!-- Custom CSS for this page -->
  <link rel="stylesheet" href="../assets/css/incoming-reservation.css">
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
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Admin</a></div>
              <div class="breadcrumb-item">Incoming Reservation</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Incoming Reservation</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Reservation Table</h4>
                  </div>
                  <div class="card-body">
                    <!-- Filter tabs -->
                    <div class="row mb-3">
                      <div class="col-12">
                        <ul class="nav nav-pills">
                          <li class="nav-item">
                            <a class="nav-link active" id="pending-tab" data-toggle="pill" href="#pending" role="tab">Incoming</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="cancelled-tab" data-toggle="pill" href="#cancelled" role="tab">Cancelled</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    
                    <div class="tab-content">
                      <!-- Incoming Tab -->
                      <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        <div class="table-responsive">
                          <table class="table table-striped" id="pending-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Pet</th>
                                <th>Service</th>
                                <th>Laboratory</th>
                                <th>Date & Time</th>
                                <th>Veterinarian</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                // Query for pending reservations only (status = 0)
                                $query = mysqli_query($con, "SELECT r.*, 
                                          CONCAT(p.firstname, ' ', p.lastname) as patient_name,
                                          pt.pet_name,
                                          pt.species,
                                          c.category as service_name,
                                          l.laboratory_name,
                                          cl.clinic_name,
                                          d.fullname as doctor_name
                                          FROM reservation r
                                          LEFT JOIN patient p ON r.patient_id = p.id
                                          LEFT JOIN pet pt ON r.pet_id = pt.id
                                          LEFT JOIN category c ON r.category_id = c.id
                                          LEFT JOIN laboratory l ON r.laboratory_id = l.id
                                          LEFT JOIN clinic cl ON r.clinic_id = cl.id
                                          LEFT JOIN doctor d ON r.doctor_id = d.id
                                          WHERE r.status = 0
                                          ORDER BY r.tdate ASC, r.time ASC");
                                $count = 0;
                                while($row = mysqli_fetch_array($query)){
                                  $count += 1;
                                  
                                  // Format date and time
                                  $formatted_date = date('M d, Y', strtotime($row['tdate']));
                                  $formatted_time = $row['time'] ? date('h:i A', strtotime($row['time'])) : 'Not specified';
                              ?>
                              <tr class="status-pending">
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['patient_name'] ?: 'Unknown Patient'; ?></td>
                                <td>
                                  <?php 
                                    $pet_display = $row['pet_name'] ?: 'No Pet';
                                    if($row['species']) {
                                      $pet_display .= ' (' . ucfirst($row['species']) . ')';
                                    }
                                    echo $pet_display;
                                  ?>
                                </td>
                                <td>
                                  <span class="badge badge-info"><?php echo $row['service_name'] ?: 'Unknown Service'; ?></span>
                                </td>
                                <td><?php echo $row['laboratory_name'] ?: 'No Laboratory'; ?></td>
                                <td>
                                  <div><?php echo $formatted_date; ?></div>
                                  <small class="text-muted"><?php echo $formatted_time; ?></small>
                                </td>
                                <td>
                                  <?php if($row['doctor_name']): ?>
                                    <div>
                                      <span class="text-dark"><?php echo $row['doctor_name']; ?></span>
                                      <button class="btn btn-sm btn-warning ml-2" onclick="reassignDoctor(<?php echo $row['id']; ?>, <?php echo $row['category_id']; ?>)" data-toggle="modal" data-target="#assignDoctorModal">
                                        <i class="fas fa-sync-alt"></i> Reassign
                                      </button>
                                    </div>
                                  <?php else: ?>
                                    <button class="btn btn-sm btn-primary" onclick="assignDoctor(<?php echo $row['id']; ?>, <?php echo $row['category_id']; ?>)" data-toggle="modal" data-target="#assignDoctorModal">
                                      <i class="fas fa-user-md"></i> Assign
                                    </button>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <?php if($row['doctor_id']): ?>
                                    <button class="btn btn-sm btn-success" onclick="approveReservation(<?php echo $row['id']; ?>)">
                                      <i class="fas fa-check"></i> Approve
                                    </button>
                                  <?php else: ?>
                                    <span class="text-muted">Assign veterinarian first</span>
                                  <?php endif; ?>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <!-- Cancelled Tab -->
                      <div class="tab-pane fade" id="cancelled" role="tabpanel">
                        <div class="table-responsive">
                          <table class="table table-striped" id="cancelled-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Pet</th>
                                <th>Service</th>
                                <th>Laboratory</th>
                                <th>Date & Time</th>
                                <th>Veterinarian</th>
                                <th>Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                // Query for cancelled reservations only (status = 2)
                                $query = mysqli_query($con, "SELECT r.*, 
                                          CONCAT(p.firstname, ' ', p.lastname) as patient_name,
                                          pt.pet_name,
                                          pt.species,
                                          c.category as service_name,
                                          l.laboratory_name,
                                          cl.clinic_name,
                                          d.fullname as doctor_name
                                          FROM reservation r
                                          LEFT JOIN patient p ON r.patient_id = p.id
                                          LEFT JOIN pet pt ON r.pet_id = pt.id
                                          LEFT JOIN category c ON r.category_id = c.id
                                          LEFT JOIN laboratory l ON r.laboratory_id = l.id
                                          LEFT JOIN clinic cl ON r.clinic_id = cl.id
                                          LEFT JOIN doctor d ON r.doctor_id = d.id
                                          WHERE r.status = 2
                                          ORDER BY r.tdate ASC, r.time ASC");
                                $count = 0;
                                while($row = mysqli_fetch_array($query)){
                                  $count += 1;
                                  
                                  // Format date and time
                                  $formatted_date = date('M d, Y', strtotime($row['tdate']));
                                  $formatted_time = $row['time'] ? date('h:i A', strtotime($row['time'])) : 'Not specified';
                              ?>
                              <tr class="status-cancelled">
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['patient_name'] ?: 'Unknown Patient'; ?></td>
                                <td>
                                  <?php 
                                    $pet_display = $row['pet_name'] ?: 'No Pet';
                                    if($row['species']) {
                                      $pet_display .= ' (' . ucfirst($row['species']) . ')';
                                    }
                                    echo $pet_display;
                                  ?>
                                </td>
                                <td>
                                  <span class="badge badge-info"><?php echo $row['service_name'] ?: 'Unknown Service'; ?></span>
                                </td>
                                <td><?php echo $row['laboratory_name'] ?: 'No Laboratory'; ?></td>
                                <td>
                                  <div><?php echo $formatted_date; ?></div>
                                  <small class="text-muted"><?php echo $formatted_time; ?></small>
                                </td>
                                <td>
                                  <?php echo $row['doctor_name'] ?: '-'; ?>
                                </td>
                                <td>
                                  <span class="badge badge-danger">Cancelled</span>
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
            </div>
          </div>
        </section>
      </div>

      <!-- Assign Doctor Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="assignDoctorModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Assign Veterinarian</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST" id="assignDoctorForm">
                <input type="hidden" name="reservation_id" id="reservation_id">
                <input type="hidden" name="category_id" id="category_id">
                
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Select Veterinarian</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <i class="fas fa-user-md"></i>
                          </div>
                        </div>
                        <select name="doctor_id" class="form-control" id="doctor_select" required>
                          <option value="" selected disabled>Choose Veterinarian...</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" name="assign_doctor" form="assignDoctorForm" class="btn btn-primary">
                <i class="fas fa-user-md"></i> Assign Vet
              </button>
            </div>
          </div>
        </div>
      </div>

      <?php include('../include/footer.php'); ?>
    </div>
  </div>

  <?php
  // Handle doctor assignment
  if(isset($_POST['assign_doctor'])) {
    $reservation_id = mysqli_real_escape_string($con, $_POST['reservation_id']);
    $doctor_id = mysqli_real_escape_string($con, $_POST['doctor_id']);
    
    $assign_query = mysqli_query($con, "UPDATE reservation SET doctor_id = '$doctor_id' WHERE id = '$reservation_id'");
    
    if($assign_query) {
      echo "<script>alert('Doctor assigned successfully!'); location.replace('incoming-reservation.php');</script>";
    } else {
      echo "<script>alert('Error assigning doctor. Please try again.');</script>";
    }
  }

  // Handle reservation approval
  if(isset($_POST['approve_reservation'])) {
    $reservation_id = mysqli_real_escape_string($con, $_POST['reservation_id']);
    
    $approve_query = mysqli_query($con, "UPDATE reservation SET status = 1, approve_by = '{$_SESSION['admin_id']}' WHERE id = '$reservation_id'");
    
    if($approve_query) {
      echo "<script>alert('Reservation approved successfully!'); location.replace('incoming-reservation.php');</script>";
    } else {
      echo "<script>alert('Error approving reservation. Please try again.');</script>";
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
  
  <!-- Custom JS for this page -->
  <script src="../assets/js/incoming-reservation.js"></script>
  
  </body>
</html>