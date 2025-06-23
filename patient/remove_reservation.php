<?php
include('../include/patient_session.php');
include('../include/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];

    // Delete the reservation from the database
    $query = "DELETE FROM reservation WHERE id = '$reservation_id'";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Reservation removed successfully.'); window.location.href='reserve.php';</script>";
    } else {
        echo "<script>alert('Error removing reservation.'); window.location.href='reserve.php';</script>";
    }
}
?>
