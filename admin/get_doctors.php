<?php
include('../include/admin_session.php');

header('Content-Type: application/json');

try {
    if (isset($_POST['load_all']) && $_POST['load_all'] == true) {
        // Load all active doctors
        $query = "SELECT d.id, d.fullname, d.clinic_id, d.category_id, 
                         cl.clinic_name, c.category as category_name
                  FROM doctor d
                  LEFT JOIN clinic cl ON d.clinic_id = cl.id
                  LEFT JOIN category c ON d.category_id = c.id
                  WHERE d.active = 1
                  ORDER BY d.fullname ASC";
    } else if (isset($_POST['category_id'])) {
        // Load doctors for specific category (original functionality)
        $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
        $query = "SELECT d.id, d.fullname, d.clinic_id, d.category_id, 
                         cl.clinic_name, c.category as category_name
                  FROM doctor d
                  LEFT JOIN clinic cl ON d.clinic_id = cl.id
                  LEFT JOIN category c ON d.category_id = c.id
                  WHERE d.active = 1 AND (d.category_id = '$category_id' OR d.category_id IS NULL)
                  ORDER BY d.fullname ASC";
    } else {
        throw new Exception('Invalid request parameters');
    }
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($con));
    }
    
    $doctors = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = array(
            'id' => $row['id'],
            'fullname' => $row['fullname'],
            'clinic_name' => $row['clinic_name'],
            'category_name' => $row['category_name']
        );
    }
    
    echo json_encode(array(
        'success' => true,
        'doctors' => $doctors
    ));
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage(),
        'doctors' => array()
    ));
}
?>