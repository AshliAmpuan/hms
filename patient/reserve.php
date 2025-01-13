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
  <script src="https://www.paypal.com/sdk/js?client-id=AaQK2c3sE-7O-kRJnsvXZ-toVwFn59XKAN_20kutjnSKCnWDd1ukV20a0kEepSRorskGHvLEFkTVeyZE&currency=PHP&components=buttons&enable-funding=venmo"></script>
  <link rel="stylesheet" href="../assets/css/components.css">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="../fullcalendar/fullcalendar.min.css" />
    <script src="../fullcalendar/lib/jquery.min.js"></script>
    <script src="../fullcalendar/lib/moment.min.js"></script>
    <script src="../fullcalendar/fullcalendar.min.js"></script>
    <style>
        .fc-title{
            color: white;
        }
    </style>
    <script>

$(document).ready(function () {
    var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()
    const calendar = () => $('#calendar').fullCalendar({
        editable: true,
        events:  "fetch-event.php",
        displayEventTime: false,
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
            
            
        },
        selectable: true,
        selectHelper: true, 
        eventDidMount:function(info){
                     $(info.el).find('.fc-event-title.fc-sticky').text('60')
        },
        select: function (start, end, allDay) {
            // var title = prompt('Event Title:');
            var dateToday = moment(date).format("Y-MM-DD");
            var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
            if(start >= dateToday)
            {
                $('#date').val(start);
                // $('#calendarModal').modal();
                window.location.replace('reserve.php?date=' + start)

            }
            calendar.fullCalendar('unselect');
        },
        
        editable: true,
        eventDrop: function (event, delta) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    $.ajax({
                        url: 'edit-event.php',
                        data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                        type: "POST",
                        success: function (response) {
                            displayMessage("Updated Successfully");
                        }
                    });
                },
        eventClick: function (event) {
            // console.log(event);
            $('#checkdate').val(event.start._i);
        }

    });

    calendar()
});
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
                <h1>Reservation</h1>
            </div>
            <form action="" method="POST">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Clinic</label>
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
                        <label for="recipient-name" class="col-form-label">Category</label>
                        <select name="category" class="form-control" id="category" required>
                            
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Laboratory</label>
                        <select name="laboratory" class="form-control" id="laboratory" required>
                                                    
                                                </select>
                       
                    </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
												<label for="recipient-name" class="col-form-label">Doctor</label>
                                                <select name="doctor" class="form-control" id="doctor" required>
                            
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
										<button type="button" class="btn  btn-secondary" data-dismiss="modal">Close</button>
										<button type="submit" name="submit" class="btn  btn-primary">Save Reservation</button>
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
                          <th>
                              #
                            </th>
                            <th>Category</th>
                            <th>Doctor</th>
                            <th>Laboratory</th>
                            <th>Time</th>
                            <th>Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $patient_id = $_SESSION['patient_id'];
                            $query = mysqli_query($con, "SELECT doctor.fullname, category.category, laboratory.laboratory_name, TIME, laboratory.price FROM reservation 
                            INNER JOIN laboratory ON laboratory.id=reservation.laboratory_id INNER JOIN category ON category.id=reservation.category_id 
                            INNER JOIN doctor ON doctor.id=reservation.doctor_id WHERE reservation.patient_id = $patient_id AND add_to_checkout = 0");
                             $count = 0;
                             $price = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                              $price += $row['price'];
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['laboratory_name']; ?></td>
                            <td><?php echo $row['TIME']; ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
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
                                                  <!-- <input type="text" class="form-control" placeholder="Category" name="category"> -->
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
                                        <div id="paypal-button-container"  style="display: none" class="paypal"></div>
                                    </div>
									<div class="modal-footer" id="modalfooter">
                  
										<button type="button" id class="btn  btn-secondary" data-dismiss="modal">Close</button>
                                         <a href="savereservation.php" class="btn btn-primary">Save Reservation</a>
									</div>
								</div>
							</div>
						</div>
      </div>
      <?php
      
        if(isset($_POST['submit']))
        {
            $id = $_SESSION['patient_id'];
            $category = $_POST['category'];
            $doctor = $_POST['doctor'];
            $clinic = $_POST['clinic'];
            $laboratory = $_POST['laboratory'];
            $tdate = $_GET['date'];
            $time = $_POST['time'];

            $reference = uniqid();

            $insert = mysqli_query($con, "INSERT INTO `reservation` (`reference`, `clinic_id`, `doctor_id` , `category_id`, `laboratory_id`, `patient_id`, `tdate`, `time`)
            VALUES ('$reference', '$clinic', '$doctor',  '$category', '$laboratory', '$id', '$tdate', '$time')");

            $last_id = mysqli_insert_id($con);

            // echo "<script type='text/javascript'>
            // window.open('print.php?id=$last_id', '_blank');
            // </script>";

            echo "<script>window.location.replace('reserve.php?date=$tdate')</script>";
        }
      
      ?>
      
    </div>
  </div>

  <!-- General JS Scripts -->
  <!-- <script src="../assets/modules/jquery.min.js"></script> -->
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
 
  <script>
    $('#mop').on('change', function() {
            var mop = $('#mop').val();
        
            if(mop == '1')
            {
                $('#modalfooter').css('display', 'block');
                $('#paypal-button-container').css('display', 'none');
            }
            else if(mop == '2')
            {
                $('#paypal-button-container').css('display', 'block');
                $('#modalfooter').css('display', 'none');
            }
          } );
     $('#clinic').on('change', function() {
            var clinic = $('#clinic').val();
        
                  $.ajax({
                     url: 'category.php?clinic=' + clinic,
                     type: 'get',
                     success: function(response){
                      $('#category').empty();
                    //   $('#parent_id').val(parent_id)
                        // $('#clinic').empty();
                        $('#doctor').empty();
                      $("#category").append(response);    

                     }
                   });
          } );
    $('#category').on('change', function() {
            var category = $('#category').val();
        
                  $.ajax({
                     url: 'laboratory.php?category_id=' + category,
                     type: 'get',
                     success: function(response){
                      $('#laboratory').empty();
                    //   $('#parent_id').val(parent_id)
                        $('#doctor').empty();
                        
                      $("#laboratory").append(response);    

                     }
                   });
          } );
          $('#laboratory').on('change', function() {
            var category = $('#laboratory').val();
        
                  $.ajax({
                     url: 'doctor.php?laboratory=' + category,
                     type: 'get',
                     success: function(response){
                      $('#doctor').empty();
                    //   $('#parent_id').val(parent_id)

                      $("#doctor").append(response);    

                     }
                   });
          } );
  </script>
   <script>
        var totalPrice = $('#totalPrice').val();
        paypal.Buttons({
          createOrder: function(data, actions) {
            // setup transaction
            return actions.order.create({
                  "purchase_units": [
                  {
                      "amount": {
                          "currency_code": "PHP",
                          "value": parseFloat(totalPrice),
                      },
                  }
              ]
            });
        },
        onApprove: function(data, actions) {
            // capture funds from transaction
            return actions.order.capture().then(function(details) {
                // return fetch('/paypal-transaction-complete', {
                //     method: 'post',
                //     body: JSON.stringify({
                //         orderID: data.orderID
                //     })
                // });

                $data = { 

                    date: <?php echo $_GET['date']; ?>,
                    mop: $('#mop').val(),
                }

                $.ajax({
                    type: "POST",
                    url: 'payrol-transaction-complete.php',
                    data: $data,
                    success: function(response){
                        //if request if made successfully then the response represent the data

                        // $( "#result" ).empty().append( response );
                        alert('Payment Success!');
                        location.reload(true);
                        // response = 'success' ? location.replace('r') : alert('Something Went Wrong!');
                    }
                });
            });
        }
        }).render('#paypal-button-container');
    </script>
</body>
</html>