<?php
session_start();
include('include/connection.php');

function sendOtpEmail($toEmail, $otp) {
    // TODO: implement actual email sending here
    // For demo, we show OTP on screen since no real mail server configured
    // mail($toEmail, "Your OTP Code", "Use this OTP to reset your password: $otp");
}

$step = 1;
$message = '';
$invalidOtp = false;
$fullName = ''; // Variable to store the user's full name

if (isset($_POST['submit_email'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $foundUser  = null;
    
    // Check tables as per roles
    $queries = [
        "SELECT u.id AS user_id, u.role, CONCAT_WS(' ', c.fullname) AS fullname FROM cashier c JOIN users u ON c.user_id = u.id WHERE c.email = '$email' AND u.active = 1 LIMIT 1",
        "SELECT u.id AS user_id, u.role, CONCAT_WS(' ', d.fullname) AS fullname FROM doctor d JOIN users u ON d.user_id = u.id WHERE d.email = '$email' AND u.active = 1 LIMIT 1",
        "SELECT u.id AS user_id, u.role, CONCAT_WS(' ', p.firstname, p.lastname) AS fullname FROM patient p JOIN users u ON p.user_id = u.id WHERE p.email = '$email' AND u.active = 1 LIMIT 1"
    ];

    foreach ($queries as $query) {
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            $foundUser  = mysqli_fetch_assoc($result);
            break;
        }
    }
    
    if ($foundUser ) {
        $otp = rand(000001, 999999); // Generate 6-digit OTP
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_user_id'] = $foundUser ['user_id'];
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_role'] = $foundUser ['role'];
        $_SESSION['otp_verified'] = false;
        $_SESSION['fullname'] = $foundUser ['fullname']; // Store full name in session
        // Simulate send email
        sendOtpEmail($email, $otp);
        $message = "Email address is registered. Your OTP Code is: <b>$otp</b>";
        $step = 2;
        $invalidOtp = false;
    } else {
        $message = "Email address is not registered .";
        $step = 1;
    }
}

if (isset($_POST['submit_otp'])) {
    $enteredOtp = trim($_POST['otp']);
    if (isset($_SESSION['reset_otp']) && $_SESSION['reset_otp'] == $enteredOtp) {
        $_SESSION['otp_verified'] = true;
        $fullName = $_SESSION['fullname']; // Retrieve full name from session
        $message = "Welcome, $fullName! OTP verified successfully! You can now reset your password.";
        $step = 3;
        $invalidOtp = false;
    } else {
        $message = "Invalid OTP. Please try again.";
        $step = 2; // Stay on OTP entry step
        $invalidOtp = true;
    }
}

if (isset($_POST['resend_otp'])) {
    $otp = rand(100000, 999999); // Generate a new 6-digit OTP
    $_SESSION['reset_otp'] = $otp;
    // Simulate send email
    sendOtpEmail($_SESSION['reset_email'], $otp);
    $message = "Your OTP Code is: <b>$otp</b>";
    $step = 2; // Stay on OTP entry step
    $invalidOtp = false; // Hide resend button after resend and page reload
}

if (isset($_POST['submit_password'])) {
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        $message = "Unauthorized action. Please start over.";
        $step = 1;
    } else {
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        if ($password !== $confirm_password) {
            $message = "Passwords do not match.";
            $step = 3;
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $message = "Password must be at least 8 characters long, contain uppercase and lowercase letters, a number, and a special character.";
            $step = 3;
        } else {
            $userId = $_SESSION['reset_user_id'];
            // Hash password with md5 for compatibility with your current DB
            $hashedPassword = md5($password);
            $update = mysqli_query($con, "UPDATE users SET password = '$hashedPassword' WHERE id = $userId");
            if ($update) {
                $message = "Password updated successfully. You can now <a href='index.php'>login</a>.";
                // Clear session reset data
                unset($_SESSION['reset_otp'], $_SESSION['reset_user_id'], $_SESSION['reset_email'], $_SESSION['reset_role'], $_SESSION['otp_verified'], $_SESSION['fullname']);
                $step = 4; // Complete
            } else {
                $message = "Failed to update password. Please try again.";
                $step = 3;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Forgot Password - Shepherd Animal Clinic</title>

<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css" />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="stylesheet" href="assets/css/components.css" />

<style>
  body { 
    background: rgb(243, 243, 243); 
    min-height: 100vh; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    position: relative;
  }
  .container-reset { 
    background: white; 
    padding: 30px; 
    border-radius: 12px; 
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); /* Darker shadow */
    max-width: 400px; 
    width: 100%; 
  }
  .message { 
    margin-bottom: 15px; 
    color: red; 
    background: transparent; 
    border: none; 
  }
  /* Back button styled with green like btn-success */
  .back-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1100;
    cursor: pointer;
    display: flex;
    align-items: center;
    background-color: #28a745; /* Bootstrap success green */
    color: #fff !important;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  .back-btn i {
    margin-right: 8px;
  }
  .back-btn:hover {
    background-color: #1e7e34; /* Darker green on hover */
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.6);
    text-decoration: none;
    color: #fff !important;
  }
  /* Input Styles */
  .form-control {
      border-radius: 5px; /* Rounded corners for inputs */
      background-color: #e9ecef; /* Darker background color */
      border: 1px solid #ced4da; /* Border color */
      color: #495057; /* Text color */
  }

  .form-control:focus {
      background-color: #e2e6ea; /* Slightly darker on focus */
      border-color: #80bdff; /* Border color on focus */
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Shadow on focus */
  }
</style>
</head>
<body>

<a href="login.php" class="back-btn" title="Back to Login">
  <i class="fas fa-arrow-left"></i> Back
</a>

<div class="container-reset">
  <h3 class="text-primary mb-4">Forgot Password</h3>
  
  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>
  
  <?php if ($step === 1): ?>
    <form method="POST" novalidate>
      <div class="form-group">
        <label for="email">Enter your registered Email Address</label>
        <input type="email" id="email" name="email" class="form-control" required autofocus placeholder="Email address" />
      </div>
      <button type="submit" name="submit_email" class="btn btn-primary btn-block">Send OTP</button>
    </form>
  <?php elseif ($step === 2): ?>
    <form method="POST" novalidate>
      <div class="form-group">
        <label for="otp">Enter the OTP sent to your email</label>
        <input type="text" id="otp" name="otp" class="form-control" pattern="\d{6}" maxlength="6" required autofocus placeholder="6-digit OTP" />
      </div>
      <button type="submit" name="submit_otp" class="btn btn-primary btn-block">Verify OTP</button>
    </form>
    <?php if ($invalidOtp): ?>
    <form method="POST" novalidate class="mt-2">
      <button type="submit" name="resend_otp" class="btn btn-secondary btn-block">Resend OTP</button>
    </form>
    <?php endif; ?>
  <?php elseif ($step === 3): ?>
    <form method="POST" novalidate>
      <div class="form-group">
        <label for="password">Enter New Password</label>
        <input type="password" id="password" name="password" class="form-control" required autofocus placeholder="New password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}" />
        <small class="form-text text-muted">Password must be at least 8 characters long, contain uppercase and lowercase letters, a number, and a special character.</small>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="8" required placeholder="Confirm password" />
      </div>
      <button type="submit" name="submit_password" class="btn btn-primary btn-block">Reset Password</button>
    </form>
  <?php else: ?>
    <div class="text-center">
      <a href="login.php" class="btn btn-success">Go to Login</a>
    </div>
  <?php endif; ?>
</div>

<script src="assets/modules/jquery.min.js"></script>
<script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
