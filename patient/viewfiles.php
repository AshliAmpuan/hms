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
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
              <div class="breadcrumb-item">Results</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Downloaded Files</h2>
            <!-- <p class="section-lead">This article component is based on card and flexbox.</p> -->

            <div class="row">
                <?php
                

                    $id = $_GET['id'];

                    $query = mysqli_query($con, "SELECT * FROM reservation_results WHERE reservation_id = '$id'");
                    while($row = mysqli_fetch_array($query)){
                ?>
              <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <article class="article">
                  <div class="article-header">
                    <div class="article-image" data-background="../assets/img/news/img08.jpg">
                    </div>
                    <div class="article-title">
                      <h2><a href="#"><?php echo $row['file']; ?></a></h2>
                    </div>
                  </div>
                  <div class="article-details">
                    <p>Date: <?php echo $row['tdate']; ?></p>
                    <div class="article-cta">
                      <a href="../doctor/uploads/<?php echo $row['file']; ?>" download target="_blank" class="btn btn-primary">Download</a>
                    </div>
                  </div>
                </article>
              </div>
              <?php } ?>
            </div>
          </div>
        </section>
      </div>
      <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Doctor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
												<label for="recipient-name" class="col-form-label">Laboratory</label><br>
                        <select class="form-control h-100" multiple="multiple" name="laboratory[]">
                              <!-- <option value="#" selected disabled>Choose..</option> -->
                              <?php

                                $query = mysqli_query($con, "SELECT * FROM laboratory");
                                while($row = mysqli_fetch_array($query)){
                              ?>
                              <option value="<?php echo $row['id']; ?>"><?php echo $row['laboratory_name']; ?></option>
                              <?php } ?>
                      </select>
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
          $fullname = $_POST['fullname'];
          $contact_number = $_POST['contact_number'];
          $address = $_POST['address'];
          $laboratory = $_POST['laboratory'];
          $username = $_POST['username'];
          $password = md5($_POST['password']);

          $user = mysqli_query($con, "INSERT INTO users (`username`, `password`, `role`) VALUES ('$username', '$password', 4)");

          $user_detail = mysqli_query($con, "SELECT * FROM users WHERE username = '$username'");
          $rowUser = mysqli_fetch_array($user_detail);
          $user_id = $rowUser['id'];

          $doctor = mysqli_query($con, "INSERT INTO doctor (`fullname`, `address`, `contact_number`, `user_id`) VALUES ('$fullname', '$address', '$contact_number', '$user_id')");
                if($doctor)
                {
                    

                    $last_insert_id = mysqli_query($con, "SELECT id FROM doctor WHERE user_id = $user_id");
                    $result = mysqli_fetch_array($last_insert_id);
                    $last_id = $result['id'];
                    foreach($laboratory as $laboratorys)
                    {
                        mysqli_query($con, "INSERT INTO doctor_laboratory (`doctor_id`, `laboratory_id`) VALUES ('$last_id', '$laboratorys')");
                    }

                    
                    echo "<script>alert('Doctor Add Successfully!')</script>";
                    echo "<script>location.replace('doctor.php')</script>";
                }
                else
                {
                  echo "<script>alert('Something Went Wrong!')</script>";
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#js-example-basic-single').select2();
    });
  </script>
</body>
</html>