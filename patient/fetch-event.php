<?php
    include('../include/connection.php');
    session_start();
    
    // Get patient ID from session or parameter
    $patient_id = $_SESSION['patient_id'] ?? null; // Assuming patient_id is stored in session
    
    // Alternative: Get patient_id from GET parameter if passed
    // $patient_id = $_GET['patient_id'] ?? $_SESSION['patient_id'] ?? null;
    
    $json = array();
    $eventArray = array();
    
    if ($patient_id) {
        // Query to get reservations for specific patient
        $sqlQuery = "SELECT 
                        CONCAT(laboratory.laboratory_name, ' - ', DATE_FORMAT(reservation.time, '%H:%i')) as title,
                        laboratory.laboratory_name,
                        laboratory.id as laboratory_id,
                        reservation.id as reservation_id,
                        reservation.tdate as start,
                        reservation.time,
                        reservation.status,
                        reservation.patient_id,
                        CASE 
                            WHEN reservation.status = 0 THEN 'Pending'
                            WHEN reservation.status = 1 THEN 'Approved'
                            WHEN reservation.status = 2 THEN 'Completed'
                            WHEN reservation.status = 3 THEN 'Cancelled'
                            ELSE 'Unknown'
                        END as status_text
                    FROM reservation 
                    INNER JOIN laboratory ON reservation.laboratory_id = laboratory.id 
                    WHERE reservation.patient_id = ? 
                    AND reservation.add_to_checkout = 1 
                    ORDER BY reservation.tdate ASC, reservation.time ASC";
        
        $stmt = mysqli_prepare($con, $sqlQuery);
        mysqli_stmt_bind_param($stmt, "i", $patient_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Add color coding based on status
            $row['color'] = match($row['status']) {
                0 => '#ffc107', // Pending - Yellow
                1 => '#28a745', // Approved - Green
                2 => '#007bff', // Completed - Blue
                3 => '#dc3545', // Cancelled - Red
                default => '#6c757d' // Unknown - Gray
            };
            
            array_push($eventArray, $row);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        // If no patient_id, return empty array or error
        // You might want to handle this case differently
        error_log("No patient_id found in session");
    }
    
    mysqli_close($con);
    echo json_encode($eventArray);
?>