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
            <h1></h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Entry</a></div>
              <div class="breadcrumb-item">Users Management</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Users Management</h2>
            <!-- <p class="section-lead">
              We use 'DataTables' made by @SpryMedia. You can check the full documentation <a href="https://datatables.net/">here</a>.
            </p> -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Users Table</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                          <th>
                              #
                            </th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Role</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          
                          <?php
                          $count = 0;
                            $query = mysqli_query($con, "SELECT username, patient.firstname, patient.lastname, (SELECT CASE WHEN role = 1 THEN 'Admin' WHEN role = 2 THEN 'Cashier' ELSE 'Patient' END) as roles, 
                            (SELECT CASE WHEN users.active = 1 THEN 'active' ELSE 'Not Active' END) as status
                             FROM users INNER JOIN patient ON patient.user_id=users.id");
                           
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['roles']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                          </tr>
                          <?php } ?>
                          <?php
                            $query = mysqli_query($con, "SELECT username, doctor.fullname, (SELECT CASE WHEN role = 1 THEN 'Admin' WHEN role = 2 THEN 'Cashier' ELSE 'Patient' END) as roles, 
                            (SELECT CASE WHEN users.active = 1 THEN 'active' ELSE 'Not Active' END) as status
                             FROM users INNER JOIN doctor ON doctor.user_id=users.id");
                           
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td>Doctor</td>
                            <td><?php echo $row['status']; ?></td>
                          </tr>
                          <?php } ?>
                          <?php
                            $query = mysqli_query($con, "SELECT username, cashier.fullname, (SELECT CASE WHEN role = 1 THEN 'Admin' WHEN role = 2 THEN 'Cashier' ELSE 'Patient' END) as roles, 
                            (SELECT CASE WHEN users.active = 1 THEN 'active' ELSE 'Not Active' END) as status
                             FROM users INNER JOIN cashier ON cashier.user_id=users.id");
                           
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td>Cashier</td>
                            <td><?php echo $row['status']; ?></td>
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
</body>
</html>