<?php
include_once('../include/patient_session.php');
include_once('../include/connection.php'); // Assuming database connection

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$patient_id = $_SESSION['patient_id'];
$hasPets = false;
$categories = [];

// Function to check if patient has pets
function checkPatientPets($con, $patient_id) {
    $sql = "SELECT COUNT(*) as pet_count FROM pet WHERE patient_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['pet_count'] > 0;
}

// Function to get active categories with laboratories
function getActiveCategories($con) {
    $categories = [];
    $sql = "SELECT * FROM category WHERE active = 1";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Get laboratories for this category
            $labSql = "SELECT * FROM laboratory WHERE category_id = ?";
            $stmt = mysqli_prepare($con, $labSql);
            mysqli_stmt_bind_param($stmt, "i", $row['id']);
            mysqli_stmt_execute($stmt);
            $labResult = mysqli_stmt_get_result($stmt);
            
            $laboratories = [];
            while ($labRow = mysqli_fetch_assoc($labResult)) {
                $laboratories[] = $labRow;
            }
            
            $row['laboratories'] = $laboratories;
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Fetch data
try {
    $hasPets = checkPatientPets($con, $patient_id);
    $categories = getActiveCategories($con);
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error_message = "An error occurred while loading dashboard data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    
    <?php include('../include/title.php'); ?>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
    <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
    <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
    <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/patient-index.css">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-94034622-3');
    </script>
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            
            <?php 
            include('../include/header.php'); 
            include('../include/sidebar.php'); 
            ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Dashboard</h1>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Welcome Message -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card enhanced-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar mr-3">
                                            <div class="avatar-initial rounded-circle bg-primary text-white">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Welcome to Shepherd Pet Care System!</h5>
                                            <p class="mb-0 text-muted">
                                                Manage your pet's health and book appointments with our veterinary services.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <?php if (!empty($categories)): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="section-header">
                                    <h4>
                                        <i class="fas fa-flask mr-2"></i>
                                        Our Services
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card enhanced-card">
                                        <div class="card-header">
                                            <h5>
                                                <i class="fas fa-flask mr-2"></i>
                                                <?php echo htmlspecialchars($category['category']); ?>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($category['laboratories'])): ?>
                                                <ul class="list-group lab-list mb-3">
                                                    <?php foreach ($category['laboratories'] as $lab): ?>
                                                        <li class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span><?php echo htmlspecialchars($lab['laboratory_name']); ?></span>
                                                                <span class="lab-price">
                                                                    ₱<?php echo number_format($lab['price'], 2); ?>
                                                                </span>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted text-center py-3">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    No service type for this category.
                                                </p>
                                            <?php endif; ?>
                                            
                                            <div class="text-center">
                                                <a href="reservation.php" class="btn btn-primary btn-reservation">
                                                    <i class="fas fa-calendar-plus mr-2"></i>Make Reservation
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info alert-enhanced">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle mr-3 fa-2x text-info"></i>
                                <div>
                                    <h6 class="mb-1">No Services Available</h6>
                                    <p class="mb-0">Please check back later for available services.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quick Actions Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card enhanced-card">
                                <div class="card-header">
                                    <h5>
                                        <i class="fas fa-bolt mr-2"></i>
                                        Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="pet data.php" class="btn btn-outline-primary btn-block">
                                                <i class="fas fa-paw mr-2"></i>
                                                Manage Pets
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="reservation.php" class="btn btn-outline-success btn-block">
                                                <i class="fas fa-calendar-plus mr-2"></i>
                                                Book Appointment
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="rptreservation.php" class="btn btn-outline-info btn-block">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                View Appointments
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="profile.php" class="btn btn-outline-warning btn-block">
                                                <i class="fas fa-user-edit mr-2"></i>
                                                Edit Profile
                                            </a>
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

    <!-- Pet Registration Modal -->
    <?php if (!$hasPets): ?>
        <div class="modal fade simple-modal" id="welcomePetModal" tabindex="-1" role="dialog" 
             aria-labelledby="welcomePetModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="welcomePetModalLabel">
                            <i class="fas fa-paw mr-2"></i>Register Your Pet
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="pet-icon-simple">
                            <img src="../img/shepherd.png" alt="Shepherd Pet Care" class="shepherd-logo">
                        </div>
                        <h5 class="mb-3">Welcome to Shepherd Pet Care System!</h5>
                        <p class="text-muted mb-4">
                            To get started with our veterinary services, please register your pet first.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="skipRegistration()">
                            Maybe later
                        </button>
                        <a href="pet data.php" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Register Pet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- JavaScript Files -->
    <script src="../assets/modules/jquery.min.js"></script>
    <script src="../assets/modules/popper.js"></script>
    <script src="../assets/modules/tooltip.js"></script>
    <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="../assets/modules/moment.min.js"></script>
    <script src="../assets/js/stisla.js"></script>
    <script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
    <script src="../assets/modules/chart.min.js"></script>
    <script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
    <script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="../assets/modules/summernote/summernote-bs4.js"></script>
    <script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
    <script src="../assets/js/page/index-0.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script src="../assets/js/custom.js"></script>
    <script src="../assets/js/patient-index.js"></script>

    <script>
        // Initialize modal display based on pet registration status
        var showWelcomeModal = <?php echo $hasPets ? 'false' : 'true'; ?>;
        
        function skipRegistration() {
            $('#welcomePetModal').modal('hide');
        }
    </script>
</body>
</html>