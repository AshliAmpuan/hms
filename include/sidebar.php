<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.php">HMS</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.php">St</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="dropdown active">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="index.php">General Dashboard</a></li>
              </ul>
            </li>
            <li class="menu-header">Starter</li>
            <?php
              $id = $_SESSION['id'];
              $queryUser = mysqli_query($con, "SELECT * FROM users WHERE id = '$id'");
              $rowUser = mysqli_fetch_array($queryUser);
            
            ?>
            <?php if($rowUser['role'] == 1) { ?>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-database"></i> <span>Entry</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="users.php">Users</a></li>
                <li><a class="nav-link" href="clinic.php">Clinic</a></li>
                <li><a class="nav-link" href="cashier.php">Cashier</a></li>
                <li><a class="nav-link" href="doctor.php">Doctor</a></li>
                <li><a class="nav-link" href="category.php">Category</a></li>
                <li><a class="nav-link" href="laboratory.php">Laboratory</a></li>
              </ul>
            </li>
            <?php } else if($rowUser['role'] == 3) { ?>
              <li><a class="nav-link" href="reservation.php"><i class="far fa-square"></i> <span>Reservation</span></a></li>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-database"></i> <span>Transaction Reports</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="rptreservation.php">Reservation</a></li>
                </ul>
              </li>
              <?php } else if($rowUser['role'] == 4) { ?>
                <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-database"></i> <span>Transaction</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="pendingtransaction.php">Pending Transaction</a></li>
                <li><a class="nav-link" href="accepttransaction.php">Accepted Transaction</a></li>
                <li><a class="nav-link" href="canceltransaction.php">Cancelled Transaction</a></li>
              </ul>
            </li>
            <?php } else { ?>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-database"></i> <span>Transaction Reports</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="rptreservation.php">Reservation</a></li>
                </ul>
              </li>
            <?php } ?>
          </ul>
        </aside>
      </div>