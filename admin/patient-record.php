<?php include('../include/admin_session.php'); ?>
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
  <link rel="stylesheet" href="../assets/css/admin-pet-record.css" />
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
              <div class="breadcrumb-item">Medical Records</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Vaccination Medical Records </h2>
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
                      <select class="form-control" name="pet_filter" id="pet_filter" onchange="filterByPet(this.value)" style="width: 180px;" 
                              data-selected-pet="<?php echo isset($_GET['pet_filter']) ? htmlspecialchars($_GET['pet_filter']) : ''; ?>" disabled>
                        <option value="">All Pets</option>
                      </select>
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
  <script src="../assets/js/admin-pet-record.js"></script>

</body>
</html>