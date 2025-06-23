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
  
  <!-- Page Specific CSS -->
  <link rel="stylesheet" href="../assets/css/admin_users.css">

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
              <div class="breadcrumb-item">Users Management</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Users Management</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Users Table</h4>
                    <div class="card-header-action">
                      <select class="form-control" name="role_filter" id="role_filter" onchange="filterByRole(this.value)" style="width: 180px;">
                        <option value="">All Roles</option>
                        <option value="2" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == '2') ? 'selected' : ''; ?>>Cashier</option>
                        <option value="3" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == '3') ? 'selected' : ''; ?>>Patient</option>
                        <option value="4" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == '4') ? 'selected' : ''; ?>>Veterinarian</option>
                      </select>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $count = 0;
                          $role_filter = isset($_GET['role_filter']) ? mysqli_real_escape_string($con, $_GET['role_filter']) : '';
                          
                          // Query for patients
                          $where_clause = "";
                          if (!empty($role_filter)) {
                            $where_clause = " AND users.role = '$role_filter'";
                          }
                          
                          $query = mysqli_query($con, "SELECT users.id, username, patient.firstname, patient.lastname, users.role,
                            (SELECT CASE WHEN users.active = 1 THEN 'Active' ELSE 'Not Active' END) as status,
                            'patient' as user_type
                            FROM users INNER JOIN patient ON patient.user_id=users.id
                            WHERE 1=1 $where_clause");
                           
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                            $role_name = '';
                            switch($row['role']) {
                              case 1: $role_name = 'Admin'; break;
                              case 2: $role_name = 'Cashier'; break;
                              case 3: $role_name = 'Patient'; break;
                              case 4: $role_name = 'Veterinarian'; break;
                              default: $role_name = 'Unknown'; break;
                            }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $role_name; ?></td>
                            <td>
                              <span class="badge badge-<?php echo ($row['status'] == 'Active') ? 'success' : 'danger'; ?>">
                                <?php echo $row['status']; ?>
                              </span>
                            </td>
                            <td>
                              <button class="btn btn-danger btn-sm delete-btn" onclick="confirmDeleteUser(<?php echo $row['id']; ?>, '<?php echo $row['user_type']; ?>')">
                                <i class="fas fa-trash"></i> Delete
                              </button>
                            </td>
                          </tr>
                          <?php } ?>
                          
                          <?php
                          // Query for doctors
                          $query = mysqli_query($con, "SELECT users.id, username, doctor.fullname, users.role,
                            (SELECT CASE WHEN users.active = 1 THEN 'Active' ELSE 'Not Active' END) as status,
                            'doctor' as user_type
                            FROM users INNER JOIN doctor ON doctor.user_id=users.id
                            WHERE 1=1 $where_clause");
                           
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                            $role_name = '';
                            switch($row['role']) {
                              case 1: $role_name = 'Admin'; break;
                              case 2: $role_name = 'Cashier'; break;
                              case 3: $role_name = 'Patient'; break;
                              case 4: $role_name = 'Veterinarian'; break;
                              default: $role_name = 'Unknown'; break;
                            }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $role_name; ?></td>
                            <td>
                              <span class="badge badge-<?php echo ($row['status'] == 'Active') ? 'success' : 'danger'; ?>">
                                <?php echo $row['status']; ?>
                              </span>
                            </td>
                            <td>
                              <button class="btn btn-danger btn-sm delete-btn" onclick="confirmDeleteUser(<?php echo $row['id']; ?>, '<?php echo $row['user_type']; ?>')">
                                <i class="fas fa-trash"></i> Delete
                              </button>
                            </td>
                          </tr>
                          <?php } ?>
                          
                          <?php
                          // Query for cashiers
                          $query = mysqli_query($con, "SELECT users.id, username, cashier.fullname, users.role,
                            (SELECT CASE WHEN users.active = 1 THEN 'Active' ELSE 'Not Active' END) as status,
                            'cashier' as user_type
                            FROM users INNER JOIN cashier ON cashier.user_id=users.id
                            WHERE 1=1 $where_clause");
                           
                          while($row = mysqli_fetch_array($query)){
                            $count += 1;
                            $role_name = '';
                            switch($row['role']) {
                              case 1: $role_name = 'Admin'; break;
                              case 2: $role_name = 'Cashier'; break;
                              case 3: $role_name = 'Patient'; break;
                              case 4: $role_name = 'Veterinarian'; break;
                              default: $role_name = 'Unknown'; break;
                            }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $role_name; ?></td>
                            <td>
                              <span class="badge badge-<?php echo ($row['status'] == 'Active') ? 'success' : 'danger'; ?>">
                                <?php echo $row['status']; ?>
                              </span>
                            </td>
                            <td>
                              <button class="btn btn-danger btn-sm delete-btn" onclick="confirmDeleteUser(<?php echo $row['id']; ?>, '<?php echo $row['user_type']; ?>')">
                                <i class="fas fa-trash"></i> Delete
                              </button>
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

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this user? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
        </div>
      </div>
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
  
  <!-- Page Specific JavaScript -->
  <script src="../assets/js/admin_users.js"></script>
</body>
</html>