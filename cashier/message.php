<?php
  include('../include/cashier_session.php'); // Assuming you have cashier session handling

  // Auto-delete messages function
  function deleteMessagesAfterApproval($patient_id, $reservation_id, $con) {
    try {
      $deleteQuery = "DELETE FROM messages 
                     WHERE patient_id = ? 
                     AND reservation_id = ?
                     AND staff_type = 'cashier'";
      
      $stmt = mysqli_prepare($con, $deleteQuery);
      mysqli_stmt_bind_param($stmt, "ii", $patient_id, $reservation_id);
      
      if (mysqli_stmt_execute($stmt)) {
        $deletedCount = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return ['success' => true, 'deleted_count' => $deletedCount];
      } else {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => mysqli_error($con)];
      }
    } catch (Exception $e) {
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }
  
  // Handle AJAX requests
  if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'send_message') {
      $cashier_id = $_SESSION['cashier_id']; // Assuming cashier ID is stored in session
      $patient_id = $_POST['patient_id'];
      $reservation_id = $_POST['reservation_id'];
      $message = trim($_POST['message']);
      
      if (!empty($message)) {
        $query = "INSERT INTO messages (patient_id, reservation_id, staff_type, staff_id, message, sender_type) VALUES (?, ?, 'cashier', ?, ?, 'staff')";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "iiis", $patient_id, $reservation_id, $cashier_id, $message);
        
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
      $cashier_id = $_SESSION['cashier_id'];
      $patient_id = $_POST['patient_id'];
      $reservation_id = $_POST['reservation_id'];
      
      $query = "SELECT message, sender_type, created_at FROM messages 
                WHERE patient_id = ? AND reservation_id = ? AND staff_type = 'cashier' AND staff_id = ? 
                ORDER BY created_at ASC";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, "iii", $patient_id, $reservation_id, $cashier_id);
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
      
      // Mark messages as read for this specific reservation
      $updateQuery = "UPDATE messages SET is_read = 1 WHERE patient_id = ? AND reservation_id = ? AND staff_type = 'cashier' AND staff_id = ? AND sender_type = 'patient'";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "iii", $patient_id, $reservation_id, $cashier_id);
      mysqli_stmt_execute($updateStmt);
      mysqli_stmt_close($updateStmt);
      
      echo json_encode(['success' => true, 'messages' => $messages]);
      mysqli_stmt_close($stmt);
      exit;
    }
    
    if ($_POST['action'] == 'get_unread_counts') {
      $cashier_id = $_SESSION['cashier_id'];
      
      $query = "SELECT CONCAT(patient_id, '-', reservation_id) as patient_reservation, COUNT(*) as count 
                FROM messages 
                WHERE staff_type = 'cashier' AND staff_id = ? AND sender_type = 'patient' AND is_read = 0 
                GROUP BY patient_id, reservation_id";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, "i", $cashier_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      $counts = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['patient_reservation']] = $row['count'];
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
  <link rel="stylesheet" href="../assets/css/cashier-message.css">
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
              <div class="breadcrumb-item active"><a href="#">Cashier</a></div>
              <div class="breadcrumb-item">Messages</div>
            </div>
          </div>

          <div class="row">
            <!-- Patient List -->
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h4><i class="fas fa-cash-register"></i> Approved Unpaid Reservations</h4>
                </div>
                
                <!-- Search Box -->
                <div class="search-box">
                  <input type="text" class="form-control" id="patient-search" placeholder="Search patients...">
                </div>
                
                <div class="card-body p-0" id="patients-list">
                  <?php
                  $cashier_id = $_SESSION['cashier_id'];
                  
                  // Get only patients with approved reservations (status = 1) that haven't been paid (paid_by IS NULL)
                  $patientQuery = "SELECT DISTINCT p.id, p.firstname, p.lastname, p.contact_number, p.email,
                                   r.id as reservation_id, r.reference, r.tdate, r.time, r.status, r.add_to_checkout,
                                   COALESCE(msg_counts.unread_count, 0) as unread_count,
                                   COALESCE(msg_counts.last_message_time, r.tdate) as last_activity
                                   FROM patient p 
                                   INNER JOIN reservation r ON p.id = r.patient_id 
                                   LEFT JOIN (
                                     SELECT patient_id, reservation_id,
                                            COUNT(CASE WHEN sender_type = 'patient' AND is_read = 0 THEN 1 END) as unread_count,
                                            MAX(created_at) as last_message_time
                                     FROM messages 
                                     WHERE staff_type = 'cashier' AND staff_id = ?
                                     GROUP BY patient_id, reservation_id
                                   ) msg_counts ON p.id = msg_counts.patient_id AND r.id = msg_counts.reservation_id
                                   WHERE p.active = 1 
                                   AND r.status = 1
                                   AND r.paid_by IS NULL
                                   ORDER BY 
                                     msg_counts.unread_count DESC,
                                     last_activity DESC, 
                                     r.tdate DESC, 
                                     p.firstname ASC";
                  
                  $stmt = mysqli_prepare($con, $patientQuery);
                  mysqli_stmt_bind_param($stmt, "i", $cashier_id);
                  mysqli_stmt_execute($stmt);
                  $patientResult = mysqli_stmt_get_result($stmt);
                  
                  if (mysqli_num_rows($patientResult) > 0) {
                      while ($patient = mysqli_fetch_assoc($patientResult)) {
                          $fullname = trim($patient['firstname'] . ' ' . $patient['lastname']);
                          $patientReservationId = $patient['id'] . '-' . $patient['reservation_id'];
                          
                          echo '<div class="patient-item" data-id="'.$patient['id'].'" data-reservation-id="'.$patient['reservation_id'].'" data-name="'.htmlspecialchars($fullname).'" data-reference="'.htmlspecialchars($patient['reference']).'">';
                          echo '<div class="patient-name">';
                          echo '<span class="online-dot"></span>'.htmlspecialchars($fullname);
                          echo '</div>';
                          echo '<div class="patient-info">';
                          echo 'Ref: '.htmlspecialchars($patient['reference']).' • ';
                          // Show contact number first, then email as fallback
                          if ($patient['contact_number']) {
                              echo htmlspecialchars($patient['contact_number']);
                          } elseif ($patient['email']) {
                              echo htmlspecialchars($patient['email']);
                          } else {
                              echo 'Patient ID: '.$patient['id'];
                          }
                          echo '<br><small class="text-muted">'.date('M j, Y g:i A', strtotime($patient['tdate'].' '.$patient['time'])).'</small>';
                          echo '</div>';
                          
                          if ($patient['unread_count'] > 0) {
                              echo '<div class="unread-count">'.$patient['unread_count'].'</div>';
                          }
                          echo '</div>';
                      }
                  } else {
                      echo '<div class="patients-empty">';
                      echo '<i class="fas fa-cash-register"></i>';
                      echo '<p>No approved unpaid reservations</p>';
                      echo '<small>Patients with approved reservations awaiting payment will appear here</small>';
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
                    <small class="text-muted" id="chat-subtitle">Billing & Payment Support</small>
                  </div>
                  
                  <div class="chat-messages" id="chat-messages">
                    <div class="text-center text-muted mt-5">
                      <i class="fas fa-calendar-check fa-2x mb-3"></i>
                      <p>Select a patient to start messaging</p>
                    </div>
                  </div>
                  
                  <div class="chat-input">
                    <div class="row">
                      <div class="col-10">
                        <input type="text" class="form-control" id="message-input" placeholder="Type your message (billing support)..." disabled>
                      </div>
                      <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" id="send-btn" disabled onclick="sendMessage()">
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
  <script src="../assets/js/cashier-message.js"></script>
</body>
</html>