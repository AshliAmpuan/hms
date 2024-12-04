<?php 

include('../include/connection.php');
session_start();
$reference = $_GET['reference'];
$doctor_id = $_SESSION['doctor_id'];

mysqli_query($con, "UPDATE reservation SET `status` = 2 WHERE reference = '$reference' AND doctor_id = '$doctor_id'");

echo "<script>window.location.replace('pendingtransaction.php')</script>";