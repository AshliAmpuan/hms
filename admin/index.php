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
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  <!-- Custom Dashboard CSS -->
  <link rel="stylesheet" href="../assets/css/admin-index.css">

  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script src="../assets/js/google-analytics.js"></script>
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
            <h1>Dashboard</h1>
          </div>
          
          <!-- First Row - Original Cards -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Patient</h4>
                  </div>
                  <div class="card-body">
                  <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM patient");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-secondary">
                  <i class="fas fa-recycle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Transaction Today</h4>
                  </div>
                  <div class="card-body">
                  <?php 
                  date_default_timezone_set("Asia/Manila");
                  $tdate = date("Y-m-d");            
                  $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM transaction WHERE tdate = '$tdate'");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo number_format($row['count'], 0); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-database"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Reservation</h4>
                  </div>
                  <div class="card-body">
                  <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM reservation");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo $row['count']; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-money-bill"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Sales</h4>
                  </div>
                  <div class="card-body">
                  <?php $query = mysqli_query($con, "SELECT SUM(price) AS price FROM transaction");
                    $row = mysqli_fetch_array($query); ?>
                    ₱<?php echo $row['price'] != null ? number_format($row['price'], 2) : '0.00'; ?>
                  </div>
                </div>
              </div>
            </div>                  
          </div>

          <!-- Second Row - Combined Stats -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-user-md"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Doctors</h4>
                  </div>
                  <div class="card-body">
                  <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM doctor WHERE active = 1");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo number_format($row['count'], 0); ?>
                  </div>
                </div>
              </div>
            </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Orders Today</h4>
                  </div>
                  <div class="card-body">
                  <?php 
                  date_default_timezone_set("Asia/Manila");
                  $today = date("Y-m-d");            
                  $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM orders WHERE DATE(order_date) = '$today'");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo number_format($row['count'], 0); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Orders</h4>
                  </div>
                  <div class="card-body">
                  <?php $query = mysqli_query($con, "SELECT COUNT(*) AS count FROM orders");
                    $row = mysqli_fetch_array($query); ?>
                    <?php echo number_format($row['count'], 0); ?>
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
                    <h4>Today's Sales</h4>
                  </div>
                  <div class="card-body">
                  <?php 
                  date_default_timezone_set("Asia/Manila");
                  $today = date("Y-m-d");            
                  $query = mysqli_query($con, "SELECT SUM(price) AS price FROM transaction WHERE tdate = '$today'");
                    $row = mysqli_fetch_array($query); ?>
                    ₱<?php echo $row['price'] != null ? number_format($row['price'], 2) : '0.00'; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Third Row - Chart with Dropdown -->
          <div class="row">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 id="chartTitle">Transaction Sales - Last 7 Days</h4>
                  <div class="card-header-form">
                    <select class="form-control" id="chartTypeSelector">
                      <option value="transaction_sales">Total Revenue</option>
                      <option value="transaction_price">Medical Revenue</option>
                      <option value="orders_price">Pet Shop Revenue</option>
                    </select>
                  </div>
                </div>
                <div class="card-body">
                  <canvas id="dataChart" height="300"></canvas>
                </div>
              </div>
            </div>
          </div>

          <?php
          // Get data for the last 7 days
          date_default_timezone_set("Asia/Manila");
          $labels = array();
          
          // Generate labels for the last 7 days
          for ($i = 6; $i >= 0; $i--) {
              $date = date('Y-m-d', strtotime("-$i days"));
              $labels[] = date('M d', strtotime($date));
          }

          // Transaction Sales Data
          $transactionSalesData = array();
          for ($i = 6; $i >= 0; $i--) {
              $date = date('Y-m-d', strtotime("-$i days"));
              $query = mysqli_query($con, "SELECT COALESCE(SUM(price), 0) AS daily_sales FROM transaction WHERE tdate = '$date'");
              $row = mysqli_fetch_array($query);
              $transactionSalesData[] = floatval($row['daily_sales']);
          }

          // Transaction Count Data
          $transactionCountData = array();
          for ($i = 6; $i >= 0; $i--) {
              $date = date('Y-m-d', strtotime("-$i days"));
              $query = mysqli_query($con, "SELECT COUNT(*) AS daily_count FROM transaction WHERE tdate = '$date'");
              $row = mysqli_fetch_array($query);
              $transactionCountData[] = intval($row['daily_count']);
          }

          // Orders Price Data (using total_amount from orders table)
          $ordersPriceData = array();
          for ($i = 6; $i >= 0; $i--) {
              $date = date('Y-m-d', strtotime("-$i days"));
              $query = mysqli_query($con, "SELECT COALESCE(SUM(total_amount), 0) AS daily_price FROM orders WHERE DATE(order_date) = '$date'");
              if ($query) {
                  $row = mysqli_fetch_array($query);
                  $ordersPriceData[] = floatval($row['daily_price']);
              } else {
                  $ordersPriceData[] = 0;
              }
          }
          ?>

        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2025 <div class="bullet"></div> Design By <a href="#">AMA STUDENTS</a>
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
  
  <!-- JS Libraries -->
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

  <!-- Dashboard Chart Script -->
  <script>
    // Pass PHP data to JavaScript
    window.dashboardData = {
      labels: <?php echo json_encode($labels); ?>,
      transactionSalesData: <?php echo json_encode($transactionSalesData); ?>,
      ordersPriceData: <?php echo json_encode($ordersPriceData); ?>
    };
  </script>
  <script src="../assets/js/admin-index.js"></script>
</body>
</html>