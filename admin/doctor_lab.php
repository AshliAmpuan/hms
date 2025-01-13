<!-- <option value="#" selected disabled>Choosee..</option> -->
<?php

    include('../include/connection.php');

    $clinic = $_GET['clinic'];

    date_default_timezone_set('Asia/Manila');
                                                        $tdate = date("Y-m-d");
                                                        $query = mysqli_query($con, "SELECT id, laboratory_name FROM laboratory WHERE clinic_id = $clinic");
                                                        while($row = mysqli_fetch_array($query)){
?>

<option value="<?php echo $row['id']; ?>"><?php echo $row['laboratory_name']; ?></option>

<?php } ?>