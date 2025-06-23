<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the session file
try {
    include('../include/admin_session.php');
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session include error: ' . $e->getMessage()]);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if database connection exists
if (!isset($con) || !$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection not available']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD']]);
    exit;
}

// Check if patient_id is provided
if (!isset($_POST['patient_id']) || empty($_POST['patient_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Patient ID is required',
        'debug' => [
            'post_data' => $_POST,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
    exit;
}

$patient_id = intval($_POST['patient_id']);

// Debug: Log the patient ID
error_log("Searching for pets with patient_id: " . $patient_id);

// Function to calculate age from birth date
function calculateAge($birthDate) {
    if (empty($birthDate) || $birthDate === '0000-00-00') {
        return null;
    }
    
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    $age = $today->diff($birth);
    
    // For pets, we might want to show age in years and months for better precision
    if ($age->y > 0) {
        return $age->y;
    } elseif ($age->m > 0) {
        return $age->m . ' month' . ($age->m > 1 ? 's' : '');
    } elseif ($age->d > 0) {
        return $age->d . ' day' . ($age->d > 1 ? 's' : '');
    } else {
        return 'Less than a day';
    }
}

try {
    // First, let's verify the patient exists
    $check_patient = "SELECT id FROM patient WHERE id = ?";
    $stmt_check = mysqli_prepare($con, $check_patient);
    
    if (!$stmt_check) {
        throw new Exception('Patient check query preparation failed: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt_check, 'i', $patient_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if (mysqli_num_rows($result_check) === 0) {
        mysqli_stmt_close($stmt_check);
        echo json_encode([
            'success' => false, 
            'message' => 'Patient not found with ID: ' . $patient_id
        ]);
        exit;
    }
    mysqli_stmt_close($stmt_check);

    // Query to get pets for the specific patient
    $query = "
        SELECT 
            p.id,
            p.pet_name,
            p.species,
            p.breed,
            p.weight,
            p.sex,
            p.birth_date,
            p.created_at
        FROM pet p
        WHERE p.patient_id = ? AND p.active = 1
        ORDER BY p.pet_name
    ";
    
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        throw new Exception('Pet query preparation failed: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $patient_id);
    $execute_result = mysqli_stmt_execute($stmt);
    
    if (!$execute_result) {
        throw new Exception('Query execution failed: ' . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    $pets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pets[] = [
            'id' => $row['id'],
            'pet_name' => htmlspecialchars($row['pet_name']),
            'species' => htmlspecialchars($row['species']),
            'breed' => htmlspecialchars($row['breed']),
            'age' => calculateAge($row['birth_date']), // Calculate age dynamically
            'weight' => $row['weight'],
            'sex' => $row['sex'],
            'birth_date' => $row['birth_date'],
            'created_at' => $row['created_at']
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    // Debug: Log the number of pets found
    error_log("Found " . count($pets) . " pets for patient_id: " . $patient_id);
    
    echo json_encode([
        'success' => true,
        'pets' => $pets,
        'count' => count($pets),
        'patient_id' => $patient_id
    ]);
    
} catch (Exception $e) {
    error_log("Database error in get_pets.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'patient_id' => $patient_id
    ]);
}
?>