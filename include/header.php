<nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, <?php echo $_SESSION['username']; ?></div></>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">Logged in 5 min ago</div>
              <a href="profile.php" class="dropdown-item has-icon text-danger">
                <i class="fas fa-user"></i> Profile
              </a>
              <a href="#" data-toggle="modal" data-target="#changePassword" class="dropdown-item has-icon text-danger">
                <i class="fas fa-key"></i> Change Password
              </a>
              <a href="../logout.php" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout 
              </a>
            </div>
          </li>
        </ul>
      </nav>

     <div class="modal fade" tabindex="-1" role="dialog" id="changePassword">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    <input type="password" class="form-control" placeholder="Password" required name="new_password" minlength="8" 
                                           pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}">
                                </div>
                                <small class="form-text text-muted">Password must be at least 8 characters long, contain uppercase and lowercase letters, a number, and a special character.</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    <input type="password" class="form-control" placeholder="Password" required name="confirm_password" minlength="8">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="changepassword" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['changepassword'])) {
    $newpassword = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($newpassword != $confirm_password) {
        echo "<script>alert('Passwords do not match!')</script>";
    } 
    // Check password strength
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $newpassword)) {
        echo "<script>alert('Password must be at least 8 characters long, contain uppercase and lowercase letters, a number, and a special character.')</script>";
    } 
    else {
        $finalpassword = md5($newpassword); // Consider using a stronger hashing algorithm like password_hash()
        $user_id = $_SESSION['id'];

        mysqli_query($con, "UPDATE users SET `password` = '$finalpassword' WHERE id = $user_id");
        echo "<script>alert('Password updated successfully!')</script>"; 
    }
}
?>
