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
          $_SESSION['clinic_id'] = $res['clinic_id'];
          echo "<script>window.location.replace('cashier/index.php')</script>";
      }
      else if($rowuser['role'] == 3)
      {
          $id = $_SESSION['id'];
          $_SESSION['role'] = $rowuser['role'];
          $client = mysqli_query($con, "SELECT * FROM patient WHERE user_id = '$id'");
          $res = mysqli_fetch_array($client);
          $_SESSION['fullname'] = $res['firstname'].' '.$res['lastname'];
          $_SESSION['patient_id'] = $res['id'];
          echo "<script>window.location.replace('patient/index.php')</script>";
      }
      else if($rowuser['role'] == 4)
      {
          $id = $_SESSION['id'];
          $_SESSION['role'] = $rowuser['role'];
          $client = mysqli_query($con, "SELECT * FROM doctor WHERE user_id = '$id'");
          $res = mysqli_fetch_array($client);
          $_SESSION['fullname'] = $res['fullname'];
          $_SESSION['doctor_id'] = $res['id'];
          $_SESSION['clinic_id'] = $res['clinic_id'];
          echo "<script>window.location.replace('doctor/index.php')</script>";
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
  <meta charset="UTF-8" />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
  <title>Login &mdash; Shepherd Animal Clinic</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/components.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&amp;display=swap" rel="stylesheet" />

  <style>
    /* General Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: #333;
      line-height: 1.6;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: 'Poppins', sans-serif;
      color: #28a745;
      margin-top: 0;
      margin-bottom: 8px;
      display: inline-block;
      vertical-align: middle;
    }

    .btn {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      text-transform: uppercase;
    }

    .login-brand {
      text-align: center;
      margin-bottom: 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .login-brand img {
      width: 100px;
      height: auto;
      margin-bottom: 1.5rem; /* Increased margin below the image */
    }

    .login-brand h1 {
      width: 100%;
      margin-top: 0.5rem; /* keep spacing below image */
      text-align: center; /* center the text */
      font-weight: 600;
      font-size: 1.75rem; /* adjust size as needed */
    }

    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
      max-width: 400px;
      width: 100%;
      padding: 15px 25px;
      margin: 0 auto;
      margin-bottom: 2rem; /* Increased margin below the card */
    }

    .card-header {
      text-align: center; /* Center the header */
    }

    .card-header h4 {
      margin: 0; /* Remove default margin */
      padding: 10px 0; /* Add padding for spacing */
      font-weight: bold; /* Make the text bold */
    }

    .form-group {
      margin-top: 4px;
      margin-bottom: 8px;
    }

    .form-control {
      border-radius: 5px;
      background-color: #e9ecef;
      border: 1px solid #ced4da;
      color: #495057;
      padding: 6px 12px;
      font-size: 14px;
    }

    .form-control:focus {
      background-color: #e2e6ea;
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .simple-footer {
      font-size: 0.9rem;
      color: #666;
      text-align: center;
      margin-top: 0.75rem;
    }

    .form-group .btn {
      margin-bottom: 0.4rem;
    }

    .mt-4.text-muted.text-center {
      margin-top: 0.75rem !important;
    }

    .form-group.text-center {
      margin-top: 15px; /* Added space above buttons */
      margin-bottom: 6px;
    }
  </style>
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container">
        <div class="login-brand">
          <img src="img/shepherd.png" alt="Shepherd Animal Clinic Logo" />
          <h1 class="m-0">Shepherd Animal Clinic</h1>
        </div>

        <div class="card card-primary mx-auto">
          <div class="card-header">
            <h4 class="m-0">Login</h4> <!-- Centered Login Text -->
          </div>

          <div class="card-body">
            <form method="POST" class="needs-validation" novalidate="">
              <div class="form-group">
                <label for="email">Username</label>
                <input id="email" type="text" class="form-control" name="username" tabindex="1" required autofocus />
                <div class="invalid-feedback">Please fill in your username</div>
              </div>

              <div class="form-group">
                <div class="d-block">
                  <label for="password" class="control-label">Password</label>
                  <div class="float-right">
                    <a href="forgot_password.php" class="text-small">Forgot Password?</a>
                  </div>
                </div>
                <input id="password" type="password" class="form-control" name="password" tabindex="2" required />
                <div class="invalid-feedback">Please fill in your password</div>
              </div>

              <div class="form-group text-center">
                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
                <a href="index.php" class="btn btn-secondary btn-lg btn-block">Back</a>
              </div>
            </form>
          </div>
        </div>

        <div class="mt-4 text-muted text-center">
          Don't have an account? <a href="register.php">Create New Account</a>
        </div>

        <div class="simple-footer">
          <!-- Copyright &copy; Stisla 2018 -->
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

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>
