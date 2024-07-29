<div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
              <img
                src="../assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
            <li class="nav-item">
                <a href="index.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                  <!-- <span class="badge badge-success">4</span> -->
                </a>
              </li>
              <!-- <li class="nav-item">
                <a
                  data-bs-toggle="collapse"
                  href="#dashboard"
                  class="collapsed"
                  aria-expanded="false"
                >
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="dashboard">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="../../demo1/index.html">
                        <span class="sub-item">Dashboard 1</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li> -->
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Components</h4>
              </li>
              <li class="nav-item">
                <!-- <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-users"></i>
                  <p>Users</p>
                  <span class="caret"></span>
                </a> -->
                <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="admin.php">
                        <span class="sub-item">Admin</span>
                      </a>
                      <a href="doctor.php">
                        <span class="sub-item">Doctor</span>
                      </a>
                      <a href="patient.php">
                        <span class="sub-item">Patient</span>
                      </a>
                      <a href="cashier.php">
                        <span class="sub-item">Cashier</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#transaction">
                  <i class="fas fa-file-import"></i>
                  <p>Transaction</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="transaction">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="pending-transaction.php">
                        <span class="sub-item">Pending Transaction</span>
                      </a>
                      <a href="approve-transaction.php">
                        <span class="sub-item">Approve Transaction</span>
                      </a>
                      <a href="cancel-transaction.php">
                        <span class="sub-item">Cancel Transaction</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#reports">
                  <i class="fas fa-archive"></i>
                  <p>Reports</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="reports">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="report-transaction.php">
                        <span class="sub-item">Transaction</span>
                      </a>
                      <a href="report-users.php">
                        <span class="sub-item">Users</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>