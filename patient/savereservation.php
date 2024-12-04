<?php

    session_start();
    include('../include/connection.php');

    $patient_id = $_SESSION['patient_id'];

    date_default_timezone_set('Asia/Manila');
    $tdate = date("Y-m-d");

    $reference = uniqid();

    mysqli_query($con, "UPDATE reservation SET `add_to_checkout` = 1, `reference` = '$reference', `tdate` = '$tdate'  WHERE add_to_checkout = 0 AND patient_id = $patient_id");

    echo "<script>window.location.replace('reserve.php')</script>";
?>