<option value="#" selected disabled>Choosee..</option>
<?php

    include('../include/connection.php');

    $clinic = $_GET['clinic'];

    date_default_timezone_set('Asia/Manila');
                                                        $tdate = date("Y-m-d");
                                                        $query = mysqli_query($con, "SELECT category.id, category FROM category WHERE clinic_id = $clinic");
                                                        while($row = mysqli_fetch_array($query)){
?>

<option value="<?php echo $row['id']; ?>"><?php echo $row['category']; ?></option>

<?php } ?>