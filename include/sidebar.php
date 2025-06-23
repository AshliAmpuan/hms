<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand" >
            <a href="index.php">Shepherd Animal Clinic</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.php"></a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="dropdown active">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-tachometer-alt"></i><span>System Dashboard</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="index.php">Main Dashboard</a></li>
              </ul>
            </li>
            <li class="menu-header">Starter</li>
            <?php
              $id = $_SESSION['id'];
              $queryUser = mysqli_query($con, "SELECT * FROM users WHERE id = '$id'");
              $rowUser = mysqli_fetch_array($queryUser);
            
            ?>
            <?php if($rowUser['role'] == 1) { ?>
            <li><a class="nav-link" href="clinic.php"><i class="fas fa-hospital"></i> <span>Clinics</span></a></li> 
              <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-users"></i> <span>User Entry</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="users.php">User Management</a></li>
                <li><a class="nav-link" href="patient.php">Patients and Pets</a></li>
                <li><a class="nav-link" href="doctor.php">Veterinarians</a></li>
                <li><a class="nav-link" href="cashier.php">Cashiers</a></li>
              </ul> 
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-stethoscope"></i> <span>Medical Service</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="category.php">Category</a></li>
                <li><a class="nav-link" href="laboratory.php">Type and Tests</a></li>
              </ul> 
            </li>
            <li><a class="nav-link" href="inventory.php"><i class="fas fa-boxes"></i> <span>Product Inventory</span></a></li> 
            <li><a class="nav-link" href="incoming-reservation.php"><i class="fas fa-calendar-check"></i> <span>Incoming Reservation</span></a></li> 
            <li><a class="nav-link" href="patient-record.php"><i class="fas fa-clipboard-list"></i> <span>Pet Medical Record</span></a></li> 


            <?php } else if($rowUser['role'] == 3) { ?>

              <li><a class="nav-link" href="message.php"><i class="fas fa-envelope"></i> <span>Message</span></a></li>              
              <li class="menu-header">Pet Record</li>
              <li><a class="nav-link" href="pet data.php"><i class="fas fa-paw"></i> <span>Your Pets</span></a></li>
              <li><a class="nav-link" href="pet record.php"><i class="fas fa-file-medical"></i> <span>Pet's Medical Records</span></a></li>

              <li><a class="nav-link" href="reservation.php"><i class="fas fa-calendar-plus"></i> <span>Book Appointment</span></a></li> 
              <li><a class="nav-link" href="rptreservation.php"><i class="fas fa-history"></i> <span>Appointment History</span></a></li>

              <li class="menu-header">Pet Med Inventory</li>
              <li><a class="nav-link" href="rpshop.php"><i class="fas fa-receipt"></i> <span>Your Order History</span></a></li>
              <li><a class="nav-link" href="petshop.php"><i class="fas fa-shopping-cart"></i> <span>Pet Shop</span></a></li>

              <?php } else if($rowUser['role'] == 4) { ?>
                <li><a class="nav-link" href="message.php"><i class="fas fa-comments"></i> <span>Message</span></a></li>
                <li><a class="nav-link" href="patientrecord.php"><i class="fas fa-user-injured"></i> <span>View Pets Record</span></a></li>                

                <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-credit-card"></i> <span>Transaction</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="accepttransaction.php">Accepted Transaction</a></li>
                <li><a class="nav-link" href="canceltransaction.php">Cancelled Transaction</a></li>
              </ul>
            </li>
            <li><a class="nav-link" href="analyzer.php"><i class="fas fa-microscope"></i> <span>Laboratory AI Analyzer</span></a></li>

            <?php } else if($rowUser['role'] == 2) { ?>
                <li><a class="nav-link" href="message.php"><i class="fas fa-envelope"></i> <span>Message</span></a></li>

                <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-money-check-alt"></i> <span>Transactions</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="online-payer.php">Online Payer Transaction</a></li>
                <li><a class="nav-link" href="accept-online-payer.php">Accepted Transaction</a></li>
                <li><a class="nav-link" href="decline-online-payer.php">Cancelled Transaction</a></li>
                <li><a class="nav-link" href="rptreservation.php">Walk In Payment</a></li>
              </ul>

                <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-truck"></i> <span>Pet Product Orders</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="productorders.php">Pet Product Transaction</a></li>
                <li><a class="nav-link" href="online-orderpayer.php">Online Product Transaction</a></li>
              </ul>
            </li>
            <?php } else { ?>
              <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-chart-bar"></i> <span>Transaction Reports</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="rptreservation.php">Reservation</a></li>
                </ul>
              </li>
            <?php } ?>
          </ul>
        </aside>
      </div>