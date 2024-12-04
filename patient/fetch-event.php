<?php
    include('../include/connection.php');
    session_start();
    // $client = mysqli_query($con, "SELECT * FROM parents WHERE user_id = '$id'");
    // $res = mysqli_fetch_array($client);
    // $parent_id = $res['id'];
    $json = array();
    $sqlQuery = "SELECT CONCAT(count(reservation.id), ' - ', laboratory.laboratory_name) as title, laboratory.laboratory_name, laboratory.id, reservation.tdate as start FROM laboratory 
                                                            INNER JOIN reservation ON reservation.laboratory_id=laboratory.id WHERE add_to_checkout = 1 GROUP BY laboratory.laboratory_name, laboratory.id, reservation.tdate";

    $result = mysqli_query($con, $sqlQuery);
    $eventArray = array();
    while ($row = mysqli_fetch_assoc($result)) {    
        array_push($eventArray, $row);
    }
    mysqli_free_result($result);

    echo json_encode($eventArray);
?>