<?php
  include('../include/doctor_session.php'); // Assuming you have doctor session handling
  
  // Get patient_id and reservation_id from URL parameters
  $selected_patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;
  $selected_reservation_id = isset($_GET['reservation_id']) ? (int)$_GET['reservation_id'] : null;
  
  // Handle AJAX requests
  if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'send_message') {
      $doctor_id = $_SESSION['doctor_id']; // Assuming doctor ID is stored in session
      $patient_id = $_POST['patient_id'];
      $message = trim($_POST['message']);
      
      if (!empty($message)) {
        $query = "INSERT INTO messages (patient_id, staff_type, staff_id, message, sender_type) VALUES (?, 'doctor', ?, ?, 'staff')";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "iis", $patient_id, $doctor_id, $message);
        
        if (mysqli_stmt_execute($stmt)) {
          echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
        } else {
          echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        mysqli_stmt_close($stmt);
      } else {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
      }
      exit;
    }
    
    if ($_POST['action'] == 'load_messages') {
      $doctor_id = $_SESSION['doctor_id'];
      $patient_id = $_POST['patient_id'];
      
      $query = "SELECT message, sender_type, created_at FROM messages 
                WHERE patient_id = ? AND staff_type = 'doctor' AND staff_id = ? 
                ORDER BY created_at ASC";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, "ii", $patient_id, $doctor_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      $messages = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = [
          'message' => $row['message'],
          'sender_type' => $row['sender_type'],
          'time' => date('g:i A', strtotime($row['created_at']))
        ];
      }
      
      // Mark messages as read
      $updateQuery = "UPDATE messages SET is_read = 1 WHERE patient_id = ? AND staff_type = 'doctor' AND staff_id = ? AND sender_type = 'patient'";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "ii", $patient_id, $doctor_id);
      mysqli_stmt_execute($updateStmt);
      mysqli_stmt_close($updateStmt);
      
      echo json_encode(['success' => true, 'messages' => $messages]);
      mysqli_stmt_close($stmt);
      exit;
    }
    
    if ($_POST['action'] == 'get_unread_counts') {
      $doctor_id = $_SESSION['doctor_id'];
      
      $query = "SELECT patient_id, COUNT(*) as count 
                FROM messages 
                WHERE staff_type = 'doctor' AND staff_id = ? AND sender_type = 'patient' AND is_read = 0 
                GROUP BY patient_id";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, "i", $doctor_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      $counts = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['patient_id']] = $row['count'];
      }
      
      echo json_encode(['success' => true, 'counts' => $counts]);
      mysqli_stmt_close($stmt);
      exit;
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <?php include('../include/title.php'); ?>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/doctor-message.css">
  
  <style>
    /* Additional CSS for patient display */
    .offline-dot {
      width: 8px;
      height: 8px;
      background-color: #6c757d;
      border-radius: 50%;
      display: inline-block;
      margin-right: 8px;
    }
    
    .online-dot {
      width: 8px;
      height: 8px;
      background-color: #28a745;
      border-radius: 50%;
      display: inline-block;
      margin-right: 8px;
    }
    
    .patient-info small.text-muted {
      font-style: italic;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include('../include/header.php'); ?>
      <?php include('../include/sidebar.php'); ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Patient Messages</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Doctor</a></div>
              <div class="breadcrumb-item">Messages</div>
            </div>
          </div>

          <div class="row">
            <!-- Patient List -->
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h4>Patients</h4>
                </div>
                
                <!-- Search Box -->
                <div class="search-box">
                  <input type="text" class="form-control" id="patient-search" placeholder="Search patients...">
                </div>
                
                <div class="card-body p-0" id="patients-list">
                  <?php
                  $doctor_id = $_SESSION['doctor_id'];
                  
                  // Get all patients associated with this doctor (through reservations) with message counts
                  $patientQuery = "SELECT DISTINCT p.id, p.firstname, p.lastname, p.email, p.contact_number,
                                   COALESCE(msg_counts.unread_count, 0) as unread_count,
                                   msg_counts.last_message_time
                                   FROM patient p 
                                   INNER JOIN reservation r ON p.id = r.patient_id 
                                   LEFT JOIN (
                                     SELECT patient_id,
                                            COUNT(CASE WHEN sender_type = 'patient' AND is_read = 0 THEN 1 END) as unread_count,
                                            MAX(created_at) as last_message_time
                                     FROM messages 
                                     WHERE staff_type = 'doctor' AND staff_id = ?
                                     GROUP BY patient_id
                                   ) msg_counts ON p.id = msg_counts.patient_id
                                   WHERE r.doctor_id = ? AND p.active = 1
                                   ORDER BY 
                                     CASE WHEN msg_counts.last_message_time IS NOT NULL THEN 0 ELSE 1 END,
                                     msg_counts.last_message_time DESC, 
                                     p.firstname ASC";
                  
                  $stmt = mysqli_prepare($con, $patientQuery);
                  mysqli_stmt_bind_param($stmt, "ii", $doctor_id, $doctor_id);
                  mysqli_stmt_execute($stmt);
                  $patientResult = mysqli_stmt_get_result($stmt);
                  
                  $patientFound = false;
                  if (mysqli_num_rows($patientResult) > 0) {
                      while ($patient = mysqli_fetch_assoc($patientResult)) {
                          $fullname = trim($patient['firstname'] . ' ' . $patient['lastname']);
                          $isSelected = ($selected_patient_id && $patient['id'] == $selected_patient_id) ? 'active' : '';
                          if ($isSelected) $patientFound = true;
                          
                          echo '<div class="patient-item '.$isSelected.'" data-id="'.$patient['id'].'" data-name="'.htmlspecialchars($fullname).'">';
                          echo '<div class="patient-name">';
                          
                          // Different indicator for patients with/without messages
                          if ($patient['last_message_time']) {
                              echo '<span class="online-dot"></span>';
                          } else {
                              echo '<span class="offline-dot"></span>';
                          }
                          
                          echo htmlspecialchars($fullname);
                          echo '</div>';
                          echo '<div class="patient-info">';
                          
                          // Show contact info with priority: contact_number first, then email
                          if ($patient['contact_number']) {
                              echo htmlspecialchars($patient['contact_number']);
                          } elseif ($patient['email']) {
                              echo htmlspecialchars($patient['email']);
                          } else {
                              echo 'Patient ID: '.$patient['id'];
                          }
                          
                          // Show "New Patient" label for those without messages
                          if (!$patient['last_message_time']) {
                              echo ' <small class="text-muted">(New)</small>';
                          }
                          
                          echo '</div>';
                          
                          // Show unread count if there are unread messages
                          if ($patient['unread_count'] > 0) {
                              echo '<div class="unread-count">'.$patient['unread_count'].'</div>';
                          }
                          echo '</div>';
                      }
                  } 
                  
                  // If patient_id is provided but not found in the main query, check if it exists
                  if ($selected_patient_id && !$patientFound) {
                      $singlePatientQuery = "SELECT p.id, p.firstname, p.lastname, p.email, p.contact_number
                                            FROM patient p 
                                            INNER JOIN reservation r ON p.id = r.patient_id
                                            WHERE p.id = ? AND r.doctor_id = ? AND p.active = 1
                                            LIMIT 1";
                      $singleStmt = mysqli_prepare($con, $singlePatientQuery);
                      mysqli_stmt_bind_param($singleStmt, "ii", $selected_patient_id, $doctor_id);
                      mysqli_stmt_execute($singleStmt);
                      $singleResult = mysqli_stmt_get_result($singleStmt);
                      
                      if (mysqli_num_rows($singleResult) > 0) {
                          $patient = mysqli_fetch_assoc($singleResult);
                          $fullname = trim($patient['firstname'] . ' ' . $patient['lastname']);
                          
                          echo '<div class="patient-item active" data-id="'.$patient['id'].'" data-name="'.htmlspecialchars($fullname).'">';
                          echo '<div class="patient-name">';
                          echo '<span class="offline-dot"></span>'.htmlspecialchars($fullname);
                          echo '</div>';
                          echo '<div class="patient-info">';
                          if ($patient['contact_number']) {
                              echo htmlspecialchars($patient['contact_number']);
                          } elseif ($patient['email']) {
                              echo htmlspecialchars($patient['email']);
                          } else {
                              echo 'Patient ID: '.$patient['id'];
                          }
                          echo ' <small class="text-muted">(New)</small>';
                          echo '</div>';
                          echo '</div>';
                          $patientFound = true;
                      }
                      mysqli_stmt_close($singleStmt);
                  }
                  
                  if (!$patientFound && mysqli_num_rows($patientResult) == 0) {
                      echo '<div class="patients-empty">';
                      echo '<i class="fas fa-users"></i>';
                      echo '<p>No patients found</p>';
                      echo '<small>No patients are associated with your account</small>';
                      echo '</div>';
                  }
                  
                  mysqli_stmt_close($stmt);
                  ?>
                </div>
              </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
              <div class="card">
                <div class="chat-container">
                  <div class="chat-header">
                    <h5 class="mb-0" id="chat-title">Select a patient to chat</h5>
                    <small class="text-muted" id="chat-subtitle">Choose from the list on the left</small>
                  </div>
                  
                  <div class="chat-messages" id="chat-messages">
                    <div class="text-center text-muted mt-5">
                      <i class="fas fa-user-md fa-2x mb-3"></i>
                      <p>Select a patient to start messaging</p>
                    </div>
                  </div>
                  
                  <div class="chat-input">
                    <div class="row">
                      <div class="col-10">
                        <input type="text" class="form-control" id="message-input" placeholder="Type your message here..." disabled>
                      </div>
                      <div class="col-2">
                        <button type="button" class="btn btn-success btn-block" id="send-btn" disabled onclick="sendMessage()">
                          Send
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </section>
      </div>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  <script src="../assets/js/scripts.js"></script>
  
  <!-- Custom JS -->
  <script src="../assets/js/doctor-message.js"></script>
  
  <!-- Pass PHP variables to JavaScript -->
  <script>
    // Pass selected patient ID to JavaScript
    window.selectedPatientId = <?php echo $selected_patient_id ? $selected_patient_id : 'null'; ?>;
    window.selectedReservationId = <?php echo $selected_reservation_id ? $selected_reservation_id : 'null'; ?>;
  </script>
</body>
</html>