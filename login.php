<?php


if(isset($_POST['submit']))
{
  include('include/connection.php');

  $username = $_POST['username'];
  $password = md5($_POST['password']);

          $user = mysqli_query($con, "SELECT * FROM users WHERE username = '$username' and active = 1");
          $rowuser = mysqli_fetch_array($user);

          $checkusername = mysqli_num_rows($user);

          if($checkusername > 0)
          {
            if ($password === $rowuser['password']) {

              session_start();
              session_regenerate_id();

              $_SESSION['loggedin'] = TRUE;
              
              $_SESSION['username'] = $rowuser['username'];
              $_SESSION['id'] = $rowuser['id'];

              if($rowuser['role'] == 1)
              {
                  $_SESSION['role'] = $rowuser['role'];
                  header('location: admin/index.php');
              }
              else if($rowuser['role'] == 2) {
                  $id = $_SESSION['id'];
                  $_SESSION['role'] = $rowuser['role'];
                  $client = mysqli_query($con, "SELECT * FROM cashier WHERE user_id = '$id'");
                  $res = mysqli_fetch_array($client);
                  $_SESSION['fullname'] = $res['fullname'];
                  $_SESSION['cashier_id'] = $res['id'];
                  echo "<script>window.location.replace('cashier/index.php')</script>";
              }
              else  if($rowuser['role'] == 3)
              {
                  $id = $_SESSION['id'];
                  $_SESSION['role'] = $rowuser['role'];
                  $client = mysqli_query($con, "SELECT * FROM patient WHERE user_id = '$id'");
                  $res = mysqli_fetch_array($client);
                  $_SESSION['fullname'] = $res['firstname'].' '.$res['lastname'];
                  $_SESSION['patient_id'] = $res['id'];
                  echo "<script>window.location.replace('patient/index.php')</script>";
              }
              

            } else {
              echo "<script>alert('Invalid Password.')</script>";
            }
          } else {
              echo "<script>alert('Invalid Username or Password.')</script>";
          }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
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
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
            <h1 class="m-0 text-primary">Montesa Medical Clinic</h1>
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login</h4></div>

              <div class="card-body">
                <form method="POST" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="email">Username</label>
                    <input id="email" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Please fill in your username
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="#" class="text-small">
                          Forgot Password?
                        </a>
                      </div>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      please fill in your password
                    </div>
                  </div>

                  <!-- <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">Remember Me</label>
                    </div>
                  </div> -->

                  <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-lg btn-block">Back</a>
                  </div>
                </form>
                <!-- <div class="text-center mt-4 mb-3">
                  <div class="text-job text-muted">Login With Social</div>
                </div>
                <div class="row sm-gutters">
                  <div class="col-6">
                    <a class="btn btn-block btn-social btn-facebook">
                      <span class="fab fa-facebook"></span> Facebook
                    </a>
                  </div>
                  <div class="col-6">
                    <a class="btn btn-block btn-social btn-twitter">
                      <span class="fab fa-twitter"></span> Twitter
                    </a>                                
                  </div>
                </div> -->

              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              Don't have an account? <a href="register.php">Create One</a>
            </div>
            <div class="simple-footer">
              <!-- Copyright &copy; Stisla 2018 -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

 

  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>