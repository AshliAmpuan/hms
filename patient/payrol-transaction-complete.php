<?php 

include('../include/connection.php');

session_start();

if(isset($_POST['mop'])) {
    $patient_id = $_SESSION['patient_id'];

    date_default_timezone_set('Asia/Manila');
    $tdate = date("Y-m-d");

    $countClinic = mysqli_query($con, "SELECT reservation.id as Id, laboratory.price, reservation.clinic_id as ClinicId FROM reservation INNER JOIN laboratory ON laboratory.id=reservation.laboratory_id WHERE reservation.patient_id = $patient_id AND add_to_checkout = 0");

    while($row = mysqli_fetch_array($countClinic))
    {
        $reservation_id = $row['Id'];
        $clinic_id = $row['ClinicId'];
        $laboratory_price = $row['price'];
        $date = $_POST['date'];
        $mop = $_POST['mop'];

        $cashier = mysqli_query($con, "SELECT * FROM cashier WHERE clinic_id = $clinic_id LIMIT 1");
        $res_cashier = mysqli_fetch_array($cashier);
        $cashier_id = $res_cashier['id'];

        mysqli_query($con, "INSERT INTO transaction (`reservation_id`, `price`, `tdate`, `cashier_id`, `mop`) VALUES ('$reservation_id', '$laboratory_price', '$tdate', '$cashier_id', '$mop')");
    }

    $reference = uniqid();

    mysqli_query($con, "UPDATE reservation SET `add_to_checkout` = 1, `reference` = '$reference', `tdate` = '$tdate', `mop` = $mop  WHERE add_to_checkout = 0 AND patient_id = $patient_id");


}