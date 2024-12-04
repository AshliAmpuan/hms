<?php

    include('../include/connection.php');

    $laboratory = $_GET['laboratory'];

    date_default_timezone_set('Asia/Manila');
                                                        $tdate = date("Y-m-d");
                                                        $query = mysqli_query($con, "SELECT * FROM doctor_laboratory INNER JOIN doctor ON doctor.id=doctor_laboratory.doctor_id WHERE laboratory_id = $laboratory");
                                                        while($row = mysqli_fetch_array($query)){
?>

<option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>

<?php } ?>