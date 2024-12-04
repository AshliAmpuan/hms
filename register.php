<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Register &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/jquery-selectric/selectric.css">

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
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="login-brand">
            <h1 class="m-0 text-primary">Montesa Medical Clinic</h1>
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Register</h4></div>

              <div class="card-body">
                <form method="POST">
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="frist_name">First Name</label>
                      <input id="frist_name" type="text" class="form-control" name="firstname" required autofocus>
                    </div>
                    <div class="form-group col-6">
                      <label for="last_name">Last Name</label>
                      <input id="last_name" type="text" class="form-control" required name="lastname">
                    </div>
                  </div>

                    <div class="row">
                    <div class="form-group col-6">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" required name="email">
                    <div class="invalid-feedback">
                    </div>
                  </div>

                  <div class="form-group col-6">
                    <label for="username">Username</label>
                    <input id="username" type="text" class="form-control" required name="username">
                  </div>
                    </div>
                  

                  <div class="row">
                    <div class="form-group col-6">
                      <label for="password" class="d-block">Password</label>
                      <input id="password" type="password" class="form-control pwstrength" required data-indicator="pwindicator" name="password">
                      <div id="pwindicator" class="pwindicator">
                        <div class="bar"></div>
                        <div class="label"></div>
                      </div>
                    </div>
                    <div class="form-group col-6">
                      <label for="contact_number" class="d-block">Contact Number</label>
                      <input id="contact_number" type="text" class="form-control" required name="contact_number">
                    </div>
                  </div>

                  <div class="row">
                    <label for="contact_number" class="d-block">Address</label>
                    <textarea name="address" class="form-control" id="" required rows="6"></textarea>
                  </div>

                  <div class="form-group mt-5">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
                      Register
                    </button>
                    <a href="login.php" class="btn btn-secondary btn-lg btn-block">Do you have login? Click Here!</a>
                  </div>
                </form>
              </div>
            </div>
            <div class="simple-footer">
              <!-- Copyright &copy; Stisla 2018 -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php
    

    if(isset($_POST['submit']))
      {
        include('include/connection.php');
          $username = $_POST['username'];
          $password = md5($_POST['password']);
          $firstname = $_POST['firstname'];
          $lastname = $_POST['lastname'];
          $email = $_POST['email'];
          $contact_number = $_POST['contact_number'];
          $address = $_POST['address'];

          $users = mysqli_query($con, "INSERT INTO users (`username`, `password`, `role`) VALUES ('$username', '$password', 3)");
          if($users)
          {
              $userLast = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
              $userData = mysqli_fetch_array($userLast);
              $user_id = $userData['id'];
              $rowUser = mysqli_num_rows($userLast);
              if($rowUser > 0)
              {
                $cashier = mysqli_query($con, "INSERT INTO patient (`firstname`, `lastname`, `email`, `address`, `contact_number`, `user_id`) VALUES ('$firstname', '$lastname', '$email', '$address', '$contact_number', '$user_id')");
                if($cashier)
                {
                    echo "<script>alert('Register Successfully')</script>";
                    echo "<script>location.replace('login.php')</script>";
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
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->
  <script src="assets/modules/jquery-pwstrength/jquery.pwstrength.min.js"></script>
  <script src="assets/modules/jquery-selectric/jquery.selectric.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="assets/js/page/auth-register.js"></script>
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>