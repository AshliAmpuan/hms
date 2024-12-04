<?php

    include('../include/connection.php');

    $category_id = $_GET['category_id'];

    date_default_timezone_set('Asia/Manila');
                                                        $tdate = date("Y-m-d");
                                                        $query = mysqli_query($con, "SELECT * FROM laboratory WHERE category_id = $category_id");
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