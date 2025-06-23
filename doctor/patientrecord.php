<?php include('../include/doctor_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php include('../include/title.php'); ?>
  
  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css" />
  
  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css" />
  <link rel="stylesheet" href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
  
  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="../assets/css/components.css">
  <link rel="stylesheet" href="../assets/css/doctor-vaccination.css" />
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
            <h1>Vaccination Records</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Records</a></div>
              <div class="breadcrumb-item">Vaccination</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Vaccination Records Management</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Vaccination History</h4>
                    <div class="card-header-action d-flex align-items-center">
                      <select class="form-control mr-3" name="patient_filter" onchange="filterRecords(this.value)" style="width: 200px;">
                        <option value="">All Patients</option>
                        <?php
                        $patients = mysqli_query($con, "SELECT id, CONCAT(firstname, ' ', lastname) as name FROM patient WHERE active = 1");
                        while ($p = mysqli_fetch_array($patients)) {
                          $selected = (isset($_GET['patient_filter']) && $_GET['patient_filter'] == $p['id']) ? 'selected' : '';
                          echo '<option value="' . $p['id'] . '" ' . $selected . '>' . $p['name'] . '</option>';
                        }
                        ?>
                      </select>
                      <select class="form-control mr-3" name="pet_filter" id="pet_filter" onchange="filterByPet(this.value)" style="width: 180px;" 
                              data-selected-pet="<?php echo isset($_GET['pet_filter']) ? htmlspecialchars($_GET['pet_filter']) : ''; ?>" disabled>
                        <option value="">All Pets</option>
                      </select>
                      <button 
                        class="btn btn-primary" 
                        id="addRecordBtn" 
                        data-toggle="modal" 
                        data-target="#addModal"
                        <?php 
                        // Always show button, but enable only if both patient and pet are selected
                        if (isset($_GET['patient_filter']) && !empty($_GET['patient_filter']) && 
                            isset($_GET['pet_filter']) && !empty($_GET['pet_filter'])) {
                          echo ''; // Show enabled if both patient and pet are selected
                        } else {
                          echo 'disabled'; // Show disabled if patient or pet not selected
                        }
                        ?>
                      >
                        <i class="fas fa-plus"></i> Add Vaccination Record
                      </button>
                    </div>
                  </div>
                  
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>WT / Temp.</th>
                            <th>Vaccination/Deworming <br>Medical Record</th>
                            <th>Remark</th>
                            <th>Veterinarian</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Handle patient and pet filtering
                          $patient_filter = isset($_GET['patient_filter']) ? mysqli_real_escape_string($con, $_GET['patient_filter']) : '';
                          $pet_filter = isset($_GET['pet_filter']) ? mysqli_real_escape_string($con, $_GET['pet_filter']) : '';
                          $where_clause = 'WHERE vr.active = 1 AND vr.category_id = 3';
                          
                          if (!empty($patient_filter)) {
                            $where_clause .= " AND vr.patient_id = '$patient_filter'";
                          }
                          if (!empty($pet_filter)) {
                            $where_clause .= " AND vr.pet_id = '$pet_filter'";
                          }

                          $query = mysqli_query($con, "SELECT vr.*, 
                                         COALESCE(pt.pet_name, 'Unknown') as pet_name,
                                         d.fullname as doctor_name
                                  FROM vaccination_record vr
                                  LEFT JOIN pet pt ON vr.pet_id = pt.id
                                  LEFT JOIN doctor d ON vr.doctor_id = d.id
                                  $where_clause
                                  ORDER BY vr.vaccination_date DESC, vr.created_at DESC");
                          $count = 0;
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['vaccination_date'] ? date('M d, Y', strtotime($row['vaccination_date'])) : 'N/A'; ?></td>
                            <td>
                              <?php 
                                $weight_temp = '';
                                if ($row['weight_lbs'] && $row['temperature_celsius']) {
                                  $weight_temp = $row['weight_lbs'] . ' lbs / ' . $row['temperature_celsius'] . ' °C';
                                } elseif ($row['weight_lbs']) {
                                  $weight_temp = $row['weight_lbs'] . ' lbs';
                                } elseif ($row['temperature_celsius']) {
                                  $weight_temp = $row['temperature_celsius'] . ' °C';
                                } else {
                                  $weight_temp = '<span style="color: #6c757d; font-style: italic;">Not recorded</span>';
                                }
                                echo $weight_temp;
                              ?>
                            </td>
                            <td><?php echo $row['vaccination_notes'] ? htmlspecialchars($row['vaccination_notes']) : '<span style="color: #6c757d; font-style: italic;">No notes</span>'; ?></td>
                            <td><?php echo $row['doctor_remark'] ? htmlspecialchars($row['doctor_remark']) : '<span style="color: #6c757d; font-style: italic;">No remarks</span>'; ?></td>
                            <td>
                              <?php 
                                if($row['doctor_name']) {
                                  echo $row['doctor_name'];
                                } else {
                                  echo '<span style="color: #6c757d; font-style: italic;">Not assigned</span>';
                                }
                              ?>
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

      <!-- Add Vaccination Record Modal -->
      <div class="modal fade" id="addModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5>Add Medical Record</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
              <div class="modal-body">
                <!-- Pet is pre-selected based on filter, so we use a hidden field -->
                <input type="hidden" name="pet_id" id="selected_pet_id" />
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Vaccination Date *</label>
                      <input type="date" name="vaccination_date" class="form-control" required />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Veterinarian</label>
                      <?php
                      // Get current doctor's name
                      $current_doctor_query = mysqli_query($con, "SELECT fullname FROM doctor WHERE id = '{$_SESSION['doctor_id']}' AND active = 1");
                      $current_doctor = mysqli_fetch_array($current_doctor_query);
                      $doctor_name = $current_doctor ? $current_doctor['fullname'] : 'Unknown Doctor';
                      ?>
                      <input type="text" value="<?php echo htmlspecialchars($doctor_name); ?>" class="form-control" readonly style="background-color: #f8f9fa;" />
                      <input type="hidden" name="doctor_id" value="<?php echo $_SESSION['doctor_id']; ?>" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Weight</label>
                      <input type="number" step="0.01" name="weight_lbs" class="form-control" placeholder="Check Pet's weight (lbs)" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Temperature</label>
                      <input type="number" step="0.1" name="temperature_celsius" class="form-control" placeholder="Check Pet's temperature (°C)" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Vaccination Medical Record *</label>
                  <textarea name="vaccination_notes" class="form-control" rows="3" placeholder="Vaccination details, vaccine type, etc." required></textarea>
                </div>
                <div class="form-group">
                  <label>Remark</label>
                  <textarea name="doctor_remark" class="form-control" rows="3" placeholder="Doctor's remarks and observations"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_record" class="btn btn-primary">Save Record</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php include('../include/footer.php'); ?>
    </div>
  </div>

  <?php
  // Add Vaccination Record
  if (isset($_POST['add_record'])) {
    $patient_id = mysqli_real_escape_string($con, $_GET['patient_filter'] ?? '');
    $pet_id = mysqli_real_escape_string($con, $_POST['pet_id']);
    $vaccination_date = mysqli_real_escape_string($con, $_POST['vaccination_date']);
    $doctor_id = mysqli_real_escape_string($con, $_POST['doctor_id']);
    $weight = $_POST['weight_lbs'] ? mysqli_real_escape_string($con, $_POST['weight_lbs']) : 'NULL';
    $temperature = $_POST['temperature_celsius'] ? mysqli_real_escape_string($con, $_POST['temperature_celsius']) : 'NULL';
    $doctor_remark = mysqli_real_escape_string($con, $_POST['doctor_remark']);
    $vaccination_notes = mysqli_real_escape_string($con, $_POST['vaccination_notes']);

    if (empty($patient_id)) {
      echo "<script>alert('Please select a patient filter from the main page before adding a record.');</script>";
    } else {
      $sql = "INSERT INTO vaccination_record (pet_id, patient_id, doctor_id, category_id, vaccination_date, weight_lbs, temperature_celsius, doctor_remark, vaccination_notes, active)
      VALUES ('$pet_id', '$patient_id', '$doctor_id', '3', '$vaccination_date', $weight, $temperature, '$doctor_remark', '$vaccination_notes', 1)";

      if (mysqli_query($con, $sql)) {
        $redirect_url = $_SERVER['PHP_SELF'];
        if (isset($_GET['patient_filter']) && !empty($_GET['patient_filter'])) {
          $redirect_url .= '?patient_filter=' . urlencode($_GET['patient_filter']);
        }
        echo "<script>alert('Vaccination record added successfully!'); window.location.href = 'patientrecord.php';</script>";
        exit();
      } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
      }
    }
  }

  // ... The rest of update and delete code unchanged ...
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
  <script src="../assets/js/doctor-vaccination.js"></script>

  <script>
    function filterRecords(patientId) {
      if(patientId) {
        window.location.href = '?patient_filter=' + patientId;
      } else {
        window.location.href = window.location.pathname;
      }
    }

    function filterByPet(petId) {
      const currentUrl = new URL(window.location);
      if(petId) {
        currentUrl.searchParams.set('pet_filter', petId);
      } else {
        currentUrl.searchParams.delete('pet_filter');
      }
      window.location.href = currentUrl.toString();
    }
  </script>
</body>
</html>