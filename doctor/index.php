<?php
  include('../include/doctor_session.php');
  
  // Auto-remove past reservations (optional - you can also do this via cron job)
  $cleanup_sql = "UPDATE reservation SET status = 2 WHERE tdate < CURDATE() AND status = 1";
  $con->query($cleanup_sql);
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
          
          <!-- Summary Cards -->
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Today's Appointments</h4>
                  </div>
                  <div class="card-body">
                    <?php
                      $doctor_id = $_SESSION['doctor_id'];
                      $today_sql = "SELECT COUNT(*) as count FROM reservation 
                                   WHERE doctor_id = ? AND status = 1 AND tdate = CURDATE()";
                      $today_stmt = $con->prepare($today_sql);
                      $today_stmt->bind_param("i", $doctor_id);
                      $today_stmt->execute();
                      $today_result = $today_stmt->get_result();
                      $today_row = $today_result->fetch_assoc();
                      echo $today_row['count'];
                      $today_stmt->close();
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="far fa-clock"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Incoming Reservation</h4>
                  </div>
                  <div class="card-body">
                    <?php
                      $pending_sql = "SELECT COUNT(*) as count FROM reservation 
                                     WHERE doctor_id = ? AND status = 1 AND (results IS NULL OR results = '') 
                                     AND tdate >= CURDATE()";
                      $pending_stmt = $con->prepare($pending_sql);
                      $pending_stmt->bind_param("i", $doctor_id);
                      $pending_stmt->execute();
                      $pending_result = $pending_stmt->get_result();
                      $pending_row = $pending_result->fetch_assoc();
                      echo $pending_row['count'];
                      $pending_stmt->close();
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Completed</h4>
                  </div>
                  <div class="card-body">
                    <?php
                      $completed_sql = "SELECT COUNT(*) as count FROM reservation 
                                       WHERE doctor_id = ? AND status = 1 AND results IS NOT NULL AND results != ''
                                       AND tdate >= CURDATE()";
                      $completed_stmt = $con->prepare($completed_sql);
                      $completed_stmt->bind_param("i", $doctor_id);
                      $completed_stmt->execute();
                      $completed_result = $completed_stmt->get_result();
                      $completed_row = $completed_result->fetch_assoc();
                      echo $completed_row['count'];
                      $completed_stmt->close();
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>This Week</h4>
                  </div>
                  <div class="card-body">
                    <?php
                      $week_sql = "SELECT COUNT(*) as count FROM reservation 
                                  WHERE doctor_id = ? AND status = 1 
                                  AND tdate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
                      $week_stmt = $con->prepare($week_sql);
                      $week_stmt->bind_param("i", $doctor_id);
                      $week_stmt->execute();
                      $week_result = $week_stmt->get_result();
                      $week_row = $week_result->fetch_assoc();
                      echo $week_row['count'];
                      $week_stmt->close();
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Today's Reservations Section -->
          <?php
            // Get today's reservations separately
            $today_reservations_sql = "SELECT reservation.*, 
                                             laboratory.laboratory_name, 
                                             patient.firstname, 
                                             patient.lastname, 
                                             patient.contact_number,
                                             pet.pet_name, 
                                             pet.species, 
                                             pet.breed, 
                                             pet.age, 
                                             pet.weight
                                     FROM reservation 
                                     INNER JOIN laboratory ON laboratory.id = reservation.laboratory_id 
                                     INNER JOIN patient ON patient.id = reservation.patient_id 
                                     LEFT JOIN pet ON pet.id = reservation.pet_id
                                     WHERE reservation.doctor_id = ? 
                                     AND reservation.status = 1
                                     AND reservation.tdate = CURDATE()
                                     ORDER BY reservation.time ASC";
            
            $today_reservations_stmt = $con->prepare($today_reservations_sql);
            $today_reservations_stmt->bind_param("i", $doctor_id);
            $today_reservations_stmt->execute();
            $today_reservations_result = $today_reservations_stmt->get_result();
            
            if($today_reservations_result->num_rows > 0) {
          ?>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4><i class="fas fa-calendar-day"></i> Today's Reservations</h4>
                  <div class="card-header-action">
                    <span class="badge badge-warning">
                      <?php echo $today_reservations_result->num_rows; ?>
                    </span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Patient</th>
                          <th>Pet</th>
                          <th>Service Type/Test</th>
                          <th>Contact</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while($today_row = $today_reservations_result->fetch_assoc()) { ?>
                        <tr>
                          <td>
                            <strong><?php echo date('M d, Y', strtotime($today_row['tdate'])); ?></strong>
                          </td>
                          <td>
                            <?php 
                              if($today_row['time']) {
                                echo '<span class="badge badge-success">' . date('h:i A', strtotime($today_row['time'])) . '</span>';
                              } else {
                                echo '<span class="badge badge-secondary">Not set</span>';
                              }
                            ?>
                          </td>
                          <td>
                            <div class="font-weight-600"><?php echo htmlspecialchars($today_row['firstname'] . ' ' . $today_row['lastname']); ?></div>
                            <div class="text-small text-muted">Ref: <?php echo htmlspecialchars($today_row['reference']); ?></div>
                          </td>
                          <td>
                            <?php if(!empty($today_row['pet_name'])) { ?>
                              <div class="font-weight-600"><?php echo htmlspecialchars($today_row['pet_name']); ?></div>
                              <?php if(!empty($today_row['species']) || !empty($today_row['breed']) || !empty($today_row['age']) || !empty($today_row['weight'])) { ?>
                                <div class="text-small text-muted">
                                  <?php 
                                    $pet_details = array();
                                    if(!empty($today_row['species'])) $pet_details[] = htmlspecialchars($today_row['species']);
                                    if(!empty($today_row['breed'])) $pet_details[] = htmlspecialchars($today_row['breed']);
                                    if(!empty($today_row['age'])) $pet_details[] = htmlspecialchars($today_row['age']) . 'y';
                                    if(!empty($today_row['weight'])) $pet_details[] = htmlspecialchars($today_row['weight']) . 'lbs';
                                    echo implode(' • ', $pet_details);
                                  ?>
                                </div>
                              <?php } ?>
                            <?php } else { ?>
                              <span class="text-muted">No pet assigned</span>
                            <?php } ?>
                          </td>
                          <td>
                            <span class="badge badge-primary"><?php echo htmlspecialchars($today_row['laboratory_name']); ?></span>
                          </td>
                          <td>
                            <?php if(!empty($today_row['contact_number'])) { ?>
                              <a href="tel:<?php echo htmlspecialchars($today_row['contact_number']); ?>" class="text-primary">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($today_row['contact_number']); ?>
                              </a>
                            <?php } else { ?>
                              <span class="text-muted">No contact</span>
                            <?php } ?>
                          </td>
                          <td>
                            <a href="message.php?patient_id=<?php echo $today_row['patient_id']; ?>&reservation_id=<?php echo $today_row['id']; ?>" 
                               class="btn btn-primary btn-sm" title="Send Message">
                              <i class="fas fa-envelope"></i> Message
                            </a>
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
          <?php 
            }
            $today_reservations_stmt->close();
          ?>

          <!-- Upcoming Reservations (Future dates only) -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4><i class="fas fa-calendar-alt"></i> Upcoming Reservations</h4>
                  <div class="card-header-action">
                    <span class="badge badge-primary">
                      <?php
                        // Count future reservations only
                        $count_sql = "SELECT COUNT(*) as count FROM reservation 
                                     INNER JOIN laboratory ON laboratory.id = reservation.laboratory_id 
                                     INNER JOIN patient ON patient.id = reservation.patient_id 
                                     WHERE reservation.doctor_id = ? 
                                     AND reservation.status = 1
                                     AND reservation.tdate > CURDATE()";
                        $count_stmt = $con->prepare($count_sql);
                        $count_stmt->bind_param("i", $doctor_id);
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        $count_row = $count_result->fetch_assoc();
                        echo $count_row['count'];
                        $count_stmt->close();
                      ?>
                    </span>
                  </div>
                </div>
                <div class="card-body">
                  <?php
                    // Get future reservations only (excluding today)
                    $upcoming_sql = "SELECT reservation.*, 
                                           laboratory.laboratory_name, 
                                           patient.firstname, 
                                           patient.lastname, 
                                           patient.contact_number,
                                           pet.pet_name, 
                                           pet.species, 
                                           pet.breed, 
                                           pet.age, 
                                           pet.weight
                                   FROM reservation 
                                   INNER JOIN laboratory ON laboratory.id = reservation.laboratory_id 
                                   INNER JOIN patient ON patient.id = reservation.patient_id 
                                   LEFT JOIN pet ON pet.id = reservation.pet_id
                                   WHERE reservation.doctor_id = ? 
                                   AND reservation.status = 1
                                   AND reservation.tdate > CURDATE()
                                   ORDER BY reservation.tdate ASC, reservation.time ASC";
                    
                    $upcoming_stmt = $con->prepare($upcoming_sql);
                    $upcoming_stmt->bind_param("i", $doctor_id);
                    $upcoming_stmt->execute();
                    $upcoming_result = $upcoming_stmt->get_result();
                    
                    if($upcoming_result->num_rows > 0) {
                  ?>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Patient</th>
                          <th>Pet</th>
                          <th>Service Type/Test</th>
                          <th>Contact</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while($upcoming_row = $upcoming_result->fetch_assoc()) { ?>
                        <tr>
                          <td>
                            <strong><?php echo date('M d, Y', strtotime($upcoming_row['tdate'])); ?></strong>
                            <?php 
                              $reservation_date = $upcoming_row['tdate'];
                              $tomorrow = date('Y-m-d', strtotime('+1 day'));
                              
                              if($reservation_date == $tomorrow) { 
                            ?>
                              <span class="badge badge-info ml-1">Tomorrow</span>
                            <?php } ?>
                          </td>
                          <td>
                            <?php 
                              if($upcoming_row['time']) {
                                echo '<span class="badge badge-success">' . date('h:i A', strtotime($upcoming_row['time'])) . '</span>';
                              } else {
                                echo '<span class="badge badge-secondary">Not set</span>';
                              }
                            ?>
                          </td>
                          <td>
                            <div class="font-weight-600"><?php echo htmlspecialchars($upcoming_row['firstname'] . ' ' . $upcoming_row['lastname']); ?></div>
                            <div class="text-small text-muted">Ref: <?php echo htmlspecialchars($upcoming_row['reference']); ?></div>
                          </td>
                          <td>
                            <?php if(!empty($upcoming_row['pet_name'])) { ?>
                              <div class="font-weight-600"><?php echo htmlspecialchars($upcoming_row['pet_name']); ?></div>
                              <?php if(!empty($upcoming_row['species']) || !empty($upcoming_row['breed']) || !empty($upcoming_row['age']) || !empty($upcoming_row['weight'])) { ?>
                                <div class="text-small text-muted">
                                  <?php 
                                    $pet_details = array();
                                    if(!empty($upcoming_row['species'])) $pet_details[] = htmlspecialchars($upcoming_row['species']);
                                    if(!empty($upcoming_row['breed'])) $pet_details[] = htmlspecialchars($upcoming_row['breed']);
                                    if(!empty($upcoming_row['age'])) $pet_details[] = htmlspecialchars($upcoming_row['age']) . 'y';
                                    if(!empty($upcoming_row['weight'])) $pet_details[] = htmlspecialchars($upcoming_row['weight']) . 'lbs';
                                    echo implode(' • ', $pet_details);
                                  ?>
                                </div>
                              <?php } ?>
                            <?php } else { ?>
                              <span class="text-muted">No pet assigned</span>
                            <?php } ?>
                          </td>
                          <td>
                            <span class="badge badge-primary"><?php echo htmlspecialchars($upcoming_row['laboratory_name']); ?></span>
                          </td>
                          <td>
                            <?php if(!empty($upcoming_row['contact_number'])) { ?>
                              <a href="tel:<?php echo htmlspecialchars($upcoming_row['contact_number']); ?>" class="text-primary">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($upcoming_row['contact_number']); ?>
                              </a>
                            <?php } else { ?>
                              <span class="text-muted">No contact</span>
                            <?php } ?>
                          </td>
                          <td>
                            <a href="message.php?patient_id=<?php echo $upcoming_row['patient_id']; ?>&reservation_id=<?php echo $upcoming_row['id']; ?>" 
                               class="btn btn-primary btn-sm" title="Send Message">
                              <i class="fas fa-envelope"></i> Message
                            </a>
                          </td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <?php 
                    $upcoming_stmt->close();
                    } else { 
                  ?>
                  <div class="empty-state" data-height="400">
                    <div class="empty-state-icon">
                      <i class="fas fa-calendar-times"></i>
                    </div>
                    <h2>No Upcoming Reservations</h2>
                    <p class="lead">You don't have any future reservations scheduled at the moment.</p>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </section>
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

  <!-- Auto-refresh for today's appointments -->
  <script>
  $(document).ready(function() {
    // Auto-refresh every 5 minutes to update today's reservations
    setInterval(function() {
      location.reload();
    }, 300000); // 5 minutes = 300000 milliseconds
  });
  </script>
</body>
</html>