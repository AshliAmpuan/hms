<?php
include('../include/admin_session.php');
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $user_type = $_POST['user_type'];
    
    // Validate input
    if(empty($user_id) || empty($user_type)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    // Start transaction
    mysqli_autocommit($con, false);
    
    try {
        // Delete from the specific user type table first (to maintain referential integrity)
        switch($user_type) {
            case 'patient':
                $delete_specific = "DELETE FROM patient WHERE user_id = ?";
                break;
            case 'doctor':
                $delete_specific = "DELETE FROM doctor WHERE user_id = ?";
                break;
            case 'cashier':
                $delete_specific = "DELETE FROM cashier WHERE user_id = ?";
                break;
            default:
                throw new Exception('Invalid user type');
        }
        
        $stmt1 = mysqli_prepare($con, $delete_specific);
        mysqli_stmt_bind_param($stmt1, "i", $user_id);
        
        if(!mysqli_stmt_execute($stmt1)) {
            throw new Exception('Error deleting from ' . $user_type . ' table: ' . mysqli_error($con));
        }
        
        // Then delete from users table
        $delete_user = "DELETE FROM users WHERE id = ?";
        $stmt2 = mysqli_prepare($con, $delete_user);
        mysqli_stmt_bind_param($stmt2, "i", $user_id);
        
        if(!mysqli_stmt_execute($stmt2)) {
            throw new Exception('Error deleting from users table: ' . mysqli_error($con));
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        
        mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt2);
        
    } catch(Exception $e) {
        // Rollback transaction
        mysqli_rollback($con);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    // Restore autocommit
    mysqli_autocommit($con, true);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>