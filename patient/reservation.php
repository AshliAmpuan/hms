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
            <div class="row">
            <!-- <div class="col-md-12">

                <label for="">laboratory</label>
                <select class="form-control" name="" id=""></select>
            </div> -->
                <div class="col-md-12">
                    <div id="calendar"></div>
                </div>
            </div>
        </section>
        <div id="calendarModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalCenterTitle">Reservation</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
                                        <form method="POST">
											<div class="form-group">
												<label for="recipient-name" class="col-form-label">Laboratory</label>
                                                <select name="laboratory" class="form-control" id="laboratory">
                                                    <?php
                                                        date_default_timezone_set('Asia/Manila');
                                                        $tdate = date("Y-m-d");
                                                        $query = mysqli_query($con, "SELECT * FROM laboratory");
                                                        while($row = mysqli_fetch_array($query)){
                                                            $laboratory_id = $row['id'];
                                                            $capacity = $row['capacity_per_day'];
                                                            $querycountlaboratory = mysqli_query($con, "SELECT laboratory.laboratory_name, laboratory.id FROM laboratory 
                                                            INNER JOIN reservation ON reservation.laboratory_id=laboratory.id WHERE laboratory.id = '$laboratory_id' and tdate = '$tdate'");
                                                            $countlaboratory = mysqli_num_rows($querycountlaboratory);
                                                            if($countlaboratory < $capacity){
                                                    ?>
                                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['laboratory_name']; ?></option>
                                                    <?php } } ?>
                                                </select>
											</div>
                                            <div class="form-group">
												<label for="recipient-name" class="col-form-label">Time</label>
                                                <input type="time" name="time" class="form-control">
											</div>
                                            
                                            <input type="hidden" name="date" id="date">
                                            
                                    </div>
									<div class="modal-footer">
										<button type="button" class="btn  btn-secondary" data-dismiss="modal">Close</button>
										<button type="submit" name="submit" class="btn  btn-primary">Save Reservation</button>
									</div>
                                    </form>
								</div>
							</div>
						</div>
      </div>
      <?php
      
        if(isset($_POST['submit']))
        {
            $id = $_SESSION['patient_id'];
            $laboratory = $_POST['laboratory'];
            $tdate = $_POST['date'];
            $time = $_POST['time'];

            $reference = uniqid();

            $insert = mysqli_query($con, "INSERT INTO `reservation` (`reference`, `laboratory_id`, `patient_id`, `tdate`, `time`)
            VALUES ('$reference', '$laboratory', '$id', '$tdate', '$time')");

            $last_id = mysqli_insert_id($con);

            echo "<script type='text/javascript'>
            window.open('print.php?id=$last_id', '_blank');
            </script>";

            echo "<script>window.location.replace('reservation.php')</script>";
        }
      
      ?>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2018 <div class="bullet"></div> Design By <a href="https://nauval.in/">Muhamad Nauval Azhar</a>
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
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
</body>
</html>