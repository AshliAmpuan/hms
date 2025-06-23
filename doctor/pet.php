<?php
include('../include/doctor_session.php');

header('Content-Type: application/json');

if (isset($_POST['patient_id']) && !empty($_POST['patient_id'])) {
    $patient_id = mysqli_real_escape_string($con, $_POST['patient_id']);
    
    $sql = "SELECT id, pet_name as name FROM pet WHERE patient_id = '$patient_id' AND active = 1 ORDER BY pet_name";
    $result = mysqli_query($con, $sql);
    
    $pets = array();
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pets[] = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
        }
        echo json_encode(array('success' => true, 'pets' => $pets));
    } else {
        echo json_encode(array('success' => false, 'pets' => array(), 'message' => 'No pets found'));
    }
} else {
    echo json_encode(array('success' => false, 'pets' => array(), 'message' => 'No patient ID provided'));
}
?>