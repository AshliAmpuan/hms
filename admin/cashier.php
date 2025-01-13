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
              <div class="breadcrumb-item">Cashier Management</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Cashier Management</h2>
            <!-- <p class="section-lead">
              We use 'DataTables' made by @SpryMedia. You can check the full documentation <a href="https://datatables.net/">here</a>.
            </p> -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Cashier Table</h4>
                    <div class="card-header-action">
                      <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus"></i> Add Cashier</button>
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
                            <th>Fullname</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = mysqli_query($con, "SELECT *,
                            (SELECT CASE WHEN active = 1 THEN 'active' ELSE 'Not Active' END) as status
                             FROM cashier");
                             $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['address']; ?></td>
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
      <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Cashier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="POST">
                  <div class="row">
                  <div class="col-lg-12">
                        <div class="form-group">
                          <label>Clinic</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-user"></i>
                              </div>
                            </div>
                            <!-- <input type="text" class="form-control" placeholder="Category" name="category"> -->
                             <select name="clinic" class="form-control" id="">
                                <option value="#" selected disabled>Choose..</option>
                                <?php
                                    $query = mysqli_query($con, "SELECT * FROM clinic");
                                    while($row = mysqli_fetch_array($query)) {
                                ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['clinic_name']; ?></option>
                                <?php } ?>
                             </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Username</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-user"></i>
                              </div>
                            </div>
                            <input type="text" class="form-control" placeholder="Username" name="username">
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Password</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-lock"></i>
                              </div>
                            </div>
                            <input type="password" class="form-control" placeholder="Password" name="password">
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Fullname</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-user"></i>
                              </div>
                            </div>
                            <input type="text" class="form-control" placeholder="Fullname" name="fullname">
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Contact Number</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-phone"></i>
                              </div>
                            </div>
                            <input type="number" class="form-control" placeholder="Contact Number" name="contact_number">
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label>Address</label>
                          <div class="input-group">
                            <textarea name="address" class="form-control" id="" row="6"></textarea>
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

      if(isset($_POST['submit']))
      {
          $username = $_POST['username'];
          $password = md5($_POST['password']);
          $fullname = $_POST['fullname'];
          $contact_number = $_POST['contact_number'];
          $address = $_POST['address'];
          $clinic = $_POST['clinic'];

          $users = mysqli_query($con, "INSERT INTO users (`username`, `password`, `role`) VALUES ('$username', '$password', 2)");
          if($users)
          {
              $userLast = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
              $userData = mysqli_fetch_array($userLast);
              $user_id = $userData['id'];
              $rowUser = mysqli_num_rows($userLast);
              if($rowUser > 0)
              {
                $cashier = mysqli_query($con, "INSERT INTO cashier (`clinic_id`, `fullname`, `address`, `contact_number`, `user_id`) VALUES ('$clinic', '$fullname', '$address', '$contact_number', '$user_id')");
                if($cashier)
                {
                    echo "<script>alert('Cashier Add Successfully')</script>";
                    echo "<script>location.replace('cashier.php')</script>";
                }
              } else{
                    echo "<script>alert('Something Went Wrong!')</script>";
              }
          }
          else {
              echo "<script>alert('Something Went Wrong On Users!')</script>";
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