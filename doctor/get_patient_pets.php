<?php
include('../include/doctor_session.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_id'])) {
    $record_id = mysqli_real_escape_string($con, $_POST['record_id']);
    
    // Validate record_id
    if (empty($record_id) || !is_numeric($record_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid record ID',
            'record' => null
        ]);
        exit;
    }
    
    // Query to get vaccination record details
    $sql = "SELECT vr.*, 
                   CONCAT(p.firstname, ' ', p.lastname) as patient_name,
                   COALESCE(pt.pet_name, 'Unknown') as pet_name,
                   d.fullname as doctor_name
            FROM vaccination_record vr
            LEFT JOIN patient p ON vr.patient_id = p.id
            LEFT JOIN pet pt ON vr.pet_id = pt.id
            LEFT JOIN doctor d ON vr.doctor_id = d.id
            WHERE vr.id = '$record_id' AND vr.active = 1 AND vr.category_id = 3";
    
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);
        
        // Format the record data for JSON response
        $formatted_record = [
            'id' => $record['id'],
            'patient_id' => $record['patient_id'],
            'pet_id' => $record['pet_id'],
            'doctor_id' => $record['doctor_id'],
            'category_id' => $record['category_id'],
            'vaccination_date' => $record['vaccination_date'],
            'weight_lbs' => $record['weight_lbs'],
            'temperature_celsius' => $record['temperature_celsius'],
            'doctor_remark' => $record['doctor_remark'],
            'vaccination_notes' => $record['vaccination_notes'],
            'patient_name' => $record['patient_name'],
            'pet_name' => $record['pet_name'],
            'doctor_name' => $record['doctor_name'],
            'created_at' => $record['created_at'],
            'updated_at' => $record['updated_at']
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Vaccination record found',
            'record' => $formatted_record
        ]);
    } else {
        // Check if record exists but is inactive or wrong category
        $check_sql = "SELECT id, active, category_id FROM vaccination_record WHERE id = '$record_id'";
        $check_result = mysqli_query($con, $check_sql);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $check_record = mysqli_fetch_assoc($check_result);
            if ($check_record['active'] == 0) {
                $message = 'Vaccination record has been deleted';
            } elseif ($check_record['category_id'] != 3) {
                $message = 'Record is not a vaccination record';
            } else {
                $message = 'Vaccination record not accessible';
            }
        } else {
            $message = 'Vaccination record not found';
        }
        
        echo json_encode([
            'success' => false,
            'message' => $message,
            'record' => null
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method or missing record_id',
        'record' => null
    ]);
}

mysqli_close($con);
?>