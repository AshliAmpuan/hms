<?php
  include('../include/cashier_session.php');
  // session_start();
?>
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
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

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
            <h1>Dashboard</h1>
          </div>
          
          <!-- Appointment Status Cards -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Pending Appointment</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM reservation WHERE status = 0");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>    
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Approved Appointment</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM reservation WHERE status = 1");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>    
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-calendar-times"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Cancelled Appointment</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM reservation WHERE status = 2");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>       
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-secondary">
                  <i class="fas fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Patients</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM patient");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>       
          </div>
          
          <!-- Order Status Cards -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Pending Orders</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM orders WHERE order_status = 'pending'");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Completed Orders</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM orders WHERE order_status = 'completed'");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Unpaid Orders</h4>
                  </div>
                  <div class="card-body">
                    <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM orders WHERE payment_status = 'unpaid'");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Revenue</h4>
                  </div>
                  <div class="card-body">
                    <?php 
                    $query = mysqli_query($con, "SELECT SUM(total_amount) AS total FROM orders WHERE order_status = 'completed'");
                    $row = mysqli_fetch_array($query); 
                    $total_revenue = $row['total'] ? number_format($row['total'], 2) : '0.00';
                    ?>
                    ₱<?php echo $total_revenue; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6 col-md-6 col-12">
              <!-- Additional content can be added here -->
            </div>
          </div>
          <div class="row">
            <!-- Additional content sections can be added here -->
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2025 <div class="bullet"></div> Design By <a href="#">AMACC Makati Students</a>
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
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
  <script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
  <script src="../assets/modules/chart.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="../assets/modules/summernote/summernote-bs4.js"></script>
  <script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/js/page/index-0.js"></script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>