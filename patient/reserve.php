<?php
  include('../include/patient_session.php');
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
  
  <!-- Calendar CSS -->
  <link rel="stylesheet" href="../fullcalendar/fullcalendar.min.css" />
  
  <!-- Custom CSS -->
  <style>
      .fc-title {
          color: white;
      }
  </style>
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
                <h1>Reservation</h1>
                <?php 
                    // Check if 'date' is set in the URL
                    $date = isset($_GET['date']) ? $_GET['date'] : ''; // Default to an empty string if not set
                ?>
                <input type="hidden" value="<?php echo htmlspecialchars($date); ?>" id="checkdate1">
            </div>
            <form action="" method="POST">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pet" class="col-form-label">Select Pet</label>
                        <select name="pet" class="form-control" id="pet" required>
                            <option value="#" disabled selected>Choose your pet...</option>
                            <?php 
                                $patient_id = $_SESSION['patient_id'];
                                $query = mysqli_query($con, "SELECT * FROM pet WHERE patient_id = $patient_id AND active = 1 ORDER BY pet_name");
                                while($row = mysqli_fetch_array($query)){
                                    $pet_info = $row['pet_name'] . " (" . $row['species'];
                                    // Removed breed from display
                                    if(!empty($row['age'])) {
                                        $pet_info .= ", " . $row['age'] . " years old";
                                    }
                                    $pet_info .= ")";
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $pet_info; ?></option>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted">Don't see your pet? <a href="pet data.php">Add a new pet</a></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Clinics</label>
                        <select name="clinic" class="form-control" id="clinic" required>
                            <option value="#" disabled selected>Choose...</option>
                            <?php 
                                $query = mysqli_query($con, "SELECT * FROM clinic");
                                while($row = mysqli_fetch_array($query)){
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['clinic_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service</label>
                        <select name="category" class="form-control" id="category" required>
                          <option value="#" disabled selected>Choose...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Service Type/Test</label>
                        <select name="laboratory" class="form-control" id="laboratory" required>
                        <option value="#" disabled selected>Choose...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Time</label>
                        <input type="time" name="time" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="btn btn-primary">Save Reservation</button>
            </div>
            </form>
            <?php
            $patient_id = $_SESSION['patient_id'];
            $query = mysqli_query($con, "SELECT COUNT(id) as count FROM reservation WHERE patient_id = $patient_id");
            $rowCount = mysqli_fetch_array($query);
            $resCount = $rowCount['count'];
            if($resCount > 0){
            ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Reserve</h4>
                    <div class="card-header-action">
                      <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-shopping-cart"></i> Reserve</button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Pet</th>
                            <th>Service</th>
                            <th>Service Type/Test</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $patient_id = $_SESSION['patient_id'];
                            $query = mysqli_query($con, "SELECT category.category, laboratory.laboratory_name, laboratory.pet_species, reservation.time, laboratory.price, reservation.id as reservation_id, pet.pet_name, pet.species FROM reservation 
                            INNER JOIN laboratory ON laboratory.id=reservation.laboratory_id 
                            INNER JOIN category ON category.id=reservation.category_id 
                            LEFT JOIN pet ON pet.id=reservation.pet_id
                            WHERE reservation.patient_id = $patient_id AND add_to_checkout = 0");
                             $count = 0;
                             $price = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                              $price += $row['price'];
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['pet_name'] ? $row['pet_name'] . " (" . $row['species'] . ")" : "N/A"; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['laboratory_name'] . (!empty($row['pet_species']) ? " (" . $row['pet_species'] . ")" : ""); ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td>
                              <form action="remove_reservation.php" method="POST" style="display:inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this reservation?');">Remove</button>
                              </form>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <input type="hidden" id="totalPrice" value="<?php echo $price; ?>">
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
        </section>
        
        <!-- Confirmation Modal -->
        <div id="exampleModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Confirm Reservation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <select name="mop" class="form-control" id="mop">
                                                <option value="#" selected disabled>Choose..</option>
                                                <option value="1">Cash</option>
                                                <option value="2">Paypal</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="paypal-button-container" style="display: none" class="paypal"></div>
                    </div>
                    <div class="modal-footer" id="modalfooter">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="savereservation.php?date=<?php echo htmlspecialchars($date); ?>" class="btn btn-primary">Save Reservation</a>
                    </div>
                </div>
            </div>
        </div>
      </div>
      
      <?php
        // Process form submission
        if(isset($_POST['submit'])) {
            $id = $_SESSION['patient_id'];
            $pet = $_POST['pet'];
            $category = $_POST['category'];
            $clinic = $_POST['clinic'];
            $laboratory = $_POST['laboratory'];
            $tdate = $_GET['date'];
            $time = $_POST['time'];

            $reference = uniqid();

            // Note: You'll need to handle doctor assignment in your backend logic
            // Either assign a default doctor or modify your database schema
            $insert = mysqli_query($con, "INSERT INTO `reservation` (`reference`, `clinic_id`, `category_id`, `laboratory_id`, `patient_id`, `pet_id`, `tdate`, `time`)
            VALUES ('$reference', '$clinic', '$category', '$laboratory', '$id', '$pet', '$tdate', '$time')");

            $last_id = mysqli_insert_id($con);
            echo "<script>window.location.replace('reserve.php?date=$tdate')</script>";
        }
      ?>
    </div>
  </div>

  <!-- JavaScript Libraries (Order matters!) -->
  <!-- jQuery (load first) -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  
  <!-- Bootstrap and UI Libraries -->
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  
  <!-- Calendar Libraries -->
  <script src="../fullcalendar/lib/moment.min.js"></script>
  <script src="../fullcalendar/fullcalendar.min.js"></script>
  
  <!-- PayPal SDK -->
  <script src="https://www.paypal.com/sdk/js?client-id=AaQK2c3sE-7O-kRJnsvXZ-toVwFn59XKAN_20kutjnSKCnWDd1ukV20a0kEepSRorskGHvLEFkTVeyZE&currency=PHP&components=buttons&enable-funding=venmo"></script>
  
  <!-- Template Scripts -->
  <script src="../assets/js/stisla.js"></script>
  <script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
  <script src="../assets/modules/chart.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="../assets/modules/summernote/summernote-bs4.js"></script>
  <script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
  <script src="../assets/js/page/index-0.js"></script>
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
  
  <!-- Custom Reservation JavaScript (load last) -->
  <script src="../assets/js/patient-reserve.js"></script>
  
</body>
</html>