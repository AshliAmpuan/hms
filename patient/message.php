<?php
  include('../include/patient_session.php');
  
  // Handle AJAX requests
  if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'send_message') {
      $patient_id = $_SESSION['patient_id']; // Assuming patient ID is stored in session
      $staff_type = $_POST['staff_type'];
      $staff_id = $_POST['staff_id'];
      $message = trim($_POST['message']);
      
      if (!empty($message)) {
        $query = "INSERT INTO messages (patient_id, staff_type, staff_id, message, sender_type) VALUES (?, ?, ?, ?, 'patient')";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "isis", $patient_id, $staff_type, $staff_id, $message);
        
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
      $patient_id = $_SESSION['patient_id'];
      $staff_type = $_POST['staff_type'];
      $staff_id = $_POST['staff_id'];
      
      $query = "SELECT message, sender_type, created_at FROM messages 
                WHERE patient_id = ? AND staff_type = ? AND staff_id = ? 
                ORDER BY created_at ASC";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, "isi", $patient_id, $staff_type, $staff_id);
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
      $updateQuery = "UPDATE messages SET is_read = 1 WHERE patient_id = ? AND staff_type = ? AND staff_id = ? AND sender_type = 'staff'";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "isi", $patient_id, $staff_type, $staff_id);
      mysqli_stmt_execute($updateStmt);
      mysqli_stmt_close($updateStmt);
      
      echo json_encode(['success' => true, 'messages' => $messages]);
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
  
  <!-- Messages CSS -->
  <link rel="stylesheet" href="../assets/css/patient-message.css">
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
            <h1>Messages</h1>
          </div>

          <div class="row">
            <!-- Staff List -->
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h4>Staff</h4>
                </div>
                <div class="card-body p-0">
                  <!-- Doctors -->
                  <div class="p-3 bg-light">
                    <small class="text-muted"><strong>DOCTORS</strong></small>
                  </div>
                  <?php
                  $patient_id = $_SESSION['patient_id'];
                  $doctorQuery = "SELECT d.*, 
                                  COUNT(m.id) as unread_count 
                                  FROM doctor d 
                                  LEFT JOIN messages m ON d.id = m.staff_id AND m.staff_type = 'doctor' AND m.patient_id = ? AND m.sender_type = 'staff' AND m.is_read = 0
                                  WHERE d.active = 1 
                                  GROUP BY d.id 
                                  ORDER BY d.fullname ASC";
                  $stmt = mysqli_prepare($con, $doctorQuery);
                  mysqli_stmt_bind_param($stmt, "i", $patient_id);
                  mysqli_stmt_execute($stmt);
                  $doctorResult = mysqli_stmt_get_result($stmt);
                  
                  if (mysqli_num_rows($doctorResult) > 0) {
                      while ($doctor = mysqli_fetch_assoc($doctorResult)) {
                          echo '<div class="staff-item" data-type="doctor" data-id="'.$doctor['id'].'" data-name="'.htmlspecialchars($doctor['fullname']).'">';
                          echo '<div class="staff-name">';
                          echo '<span class="online-dot"></span>'.htmlspecialchars($doctor['fullname']);
                          echo '</div>';
                          echo '<div class="staff-role">Doctor</div>';
                          if ($doctor['unread_count'] > 0) {
                              echo '<div class="unread-count">'.$doctor['unread_count'].'</div>';
                          }
                          echo '</div>';
                      }
                  } else {
                      echo '<div class="p-3 text-muted">No doctors available</div>';
                  }
                  mysqli_stmt_close($stmt);
                  ?>
                  
                  <!-- Cashiers -->
                  <div class="p-3 bg-light border-top">
                    <small class="text-muted"><strong>CASHIERS</strong></small>
                  </div>
                  <?php
                  $cashierQuery = "SELECT c.*, 
                                   COUNT(m.id) as unread_count 
                                   FROM cashier c 
                                   LEFT JOIN messages m ON c.id = m.staff_id AND m.staff_type = 'cashier' AND m.patient_id = ? AND m.sender_type = 'staff' AND m.is_read = 0
                                   WHERE c.active = 1 
                                   GROUP BY c.id 
                                   ORDER BY c.fullname ASC";
                  $stmt = mysqli_prepare($con, $cashierQuery);
                  mysqli_stmt_bind_param($stmt, "i", $patient_id);
                  mysqli_stmt_execute($stmt);
                  $cashierResult = mysqli_stmt_get_result($stmt);
                  
                  if (mysqli_num_rows($cashierResult) > 0) {
                      while ($cashier = mysqli_fetch_assoc($cashierResult)) {
                          echo '<div class="staff-item" data-type="cashier" data-id="'.$cashier['id'].'" data-name="'.htmlspecialchars($cashier['fullname']).'">';
                          echo '<div class="staff-name">';
                          echo '<span class="online-dot"></span>'.htmlspecialchars($cashier['fullname']);
                          echo '</div>';
                          echo '<div class="staff-role">Cashier</div>';
                          if ($cashier['unread_count'] > 0) {
                              echo '<div class="unread-count">'.$cashier['unread_count'].'</div>';
                          }
                          echo '</div>';
                      }
                  } else {
                      echo '<div class="p-3 text-muted">No cashiers available</div>';
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
                    <h5 class="mb-0" id="chat-title">Select a staff member to chat</h5>
                    <small class="text-muted" id="chat-subtitle">Choose from the list on the left</small>
                  </div>
                  
                  <div class="chat-messages" id="chat-messages">
                    <div class="text-center text-muted mt-5">
                      <i class="fas fa-comments fa-2x mb-3"></i>
                      <p>Start a conversation</p>
                    </div>
                  </div>
                  
                  <div class="chat-input">
                    <div class="row">
                      <div class="col-10">
                        <input type="text" class="form-control" id="message-input" placeholder="Type your message here..." disabled>
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
  
  <!-- Messages JS -->
  <script src="../assets/js/patient-message.js"></script>
</body>
</html>