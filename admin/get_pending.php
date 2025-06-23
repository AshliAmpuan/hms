<?php
include('../include/admin_session.php');

header('Content-Type: application/json');

try {
    $query = mysqli_query($con, "SELECT COUNT(*) as count FROM reservation WHERE status = 0");
    
    if (!$query) {
        throw new Exception("Database query failed: " . mysqli_error($con));
    }
    
    $result = mysqli_fetch_array($query);
    $count = $result['count'];
    
    echo json_encode([
        'success' => true,
        'count' => $count,
        'message' => 'Pending count retrieved successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'count' => 0,
        'message' => 'Error getting pending count: ' . $e->getMessage()
    ]);
}
?>