<?php 
include('../include/patient_session.php'); 

// Process form submissions first (before any HTML output)
// Add Pet
if(isset($_POST['submit'])) {
    $pet_name = mysqli_real_escape_string($con, $_POST['pet_name']);
    $patient_id = $_SESSION['patient_id']; // Use logged-in patient ID
    $species = mysqli_real_escape_string($con, $_POST['species']);
    $breed = !empty($_POST['breed']) ? mysqli_real_escape_string($con, $_POST['breed']) : NULL;
    $weight = !empty($_POST['weight']) ? $_POST['weight'] : NULL;
    $sex = $_POST['sex'];
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : NULL;

    $insertQuery = "INSERT INTO pet (patient_id, pet_name, species, breed, weight, sex, birth_date) 
                   VALUES ('$patient_id', '$pet_name', '$species', " . 
                   ($breed ? "'$breed'" : "NULL") . ", " .
                   ($weight ? "'$weight'" : "NULL") . ", " .
                   "'$sex', " . ($birth_date ? "'$birth_date'" : "NULL") . ")";
    
    if(mysqli_query($con, $insertQuery)) {
        $_SESSION['success_message'] = 'Pet registered successfully!';
        header("Location: pet data.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'Something went wrong! ' . mysqli_error($con);
        header("Location: pet data.php");
        exit();
    }
}

// Update Pet
if(isset($_POST['update'])) {
    $pet_id = $_POST['pet_id'];
    $pet_name = mysqli_real_escape_string($con, $_POST['edit_pet_name']);
    $patient_id = $_SESSION['patient_id']; // Use logged-in patient ID
    $species = mysqli_real_escape_string($con, $_POST['edit_species']);
    $breed = !empty($_POST['edit_breed']) ? mysqli_real_escape_string($con, $_POST['edit_breed']) : NULL;
    $weight = !empty($_POST['edit_weight']) ? $_POST['edit_weight'] : NULL;
    $sex = $_POST['edit_sex'];
    $birth_date = !empty($_POST['edit_birth_date']) ? $_POST['edit_birth_date'] : NULL;
    $active = $_POST['edit_active'];

    $updateQuery = "UPDATE pet SET 
                   patient_id='$patient_id', 
                   pet_name='$pet_name', 
                   species='$species', 
                   breed=" . ($breed ? "'$breed'" : "NULL") . ", 
                   weight=" . ($weight ? "'$weight'" : "NULL") . ", 
                   sex='$sex', 
                   birth_date=" . ($birth_date ? "'$birth_date'" : "NULL") . ", 
                   active='$active' 
                   WHERE id='$pet_id'";
    
    if(mysqli_query($con, $updateQuery)) {
        $_SESSION['success_message'] = 'Pet information updated successfully!';
        header("Location: pet data.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'Something went wrong! ' . mysqli_error($con);
        header("Location: pet data.php");
        exit();
    }
}

// Delete Pet
if(isset($_POST['delete'])) {
    $pet_id = $_POST['delete_pet_id'];

    $deleteQuery = mysqli_query($con, "DELETE FROM pet WHERE id='$pet_id'");
    if($deleteQuery) {
        $_SESSION['success_message'] = 'Pet record deleted successfully!';
        header("Location: pet data.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'Something went wrong!';
        header("Location: pet data.php");
        exit();
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

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-94034622-3');
  </script>
  <!-- /END GA -->
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
            <h1>Pet Management</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Registration</a></div>
              <div class="breadcrumb-item">Pet Management</div>
            </div>
          </div>

          <!-- Display success/error messages -->
          <?php
          if(isset($_SESSION['success_message'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      ' . $_SESSION['success_message'] . '
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                    </div>';
              unset($_SESSION['success_message']);
          }

          if(isset($_SESSION['error_message'])) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      ' . $_SESSION['error_message'] . '
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                    </div>';
              unset($_SESSION['error_message']);
          }
          ?>

          <div class="section-body">
            <h2 class="section-title">Registered Pets</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Pets Table</h4>
                    <div class="card-header-action">
                      <button class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus"></i> Register Pet</button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Pet Name</th>
                            <th>Species</th>
                            <th>Breed</th>
                            <th>Age</th>
                            <th>Weight</th>
                            <th>Sex</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = mysqli_query($con, "SELECT 
                                p.id, p.pet_name, p.species, p.breed, p.weight, p.sex, p.birth_date, p.active, p.patient_id,
                                CONCAT(pt.firstname, ' ', pt.lastname) as owner_name,
                                CASE 
                                    WHEN p.birth_date IS NOT NULL THEN 
                                        TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE())
                                    ELSE NULL 
                                END as calculated_age
                                FROM pet p
                                INNER JOIN patient pt ON pt.id = p.patient_id 
                                WHERE p.patient_id = '".$_SESSION['patient_id']."'
                                ORDER BY p.created_at DESC");
                            $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                              $status = $row['active'] ? 'Active' : 'Inactive';
                              $statusClass = $row['active'] ? 'badge-success' : 'badge-danger';
                              $sex_display = '';
                              switch($row['sex']) {
                                case 'M': $sex_display = 'Male'; break;
                                case 'F': $sex_display = 'Female'; break;
                                default: $sex_display = 'Unknown'; break;
                              }
                              $age_display = $row['calculated_age'] !== null ? $row['calculated_age'] . ' Years old' : 'N/A';
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $row['pet_name']; ?></td>
                            <td><?php echo $row['species']; ?></td>
                            <td><?php echo $row['breed'] ?: 'Mixed'; ?></td>
                            <td><?php echo $age_display; ?></td>
                            <td><?php echo $row['weight'] ? $row['weight'] . ' lbs' : 'N/A'; ?></td>
                            <td><?php echo $sex_display; ?></td>
                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                            <td>
                              <button class="btn btn-sm btn-warning" onclick="editPet(<?php echo $row['id']; ?>, '<?php echo addslashes($row['pet_name']); ?>', '<?php echo $row['species']; ?>', '<?php echo addslashes($row['breed']); ?>', <?php echo $row['weight'] ?: 'null'; ?>, '<?php echo $row['sex']; ?>', '<?php echo $row['birth_date']; ?>', <?php echo $row['patient_id']; ?>, <?php echo $row['active']; ?>)" data-toggle="modal" data-target="#editModal">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="deletePet(<?php echo $row['id']; ?>, '<?php echo addslashes($row['pet_name']); ?>')" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Add Pet Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Register New Pet</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Pet Name</label>
                      <input type="text" class="form-control" placeholder="Pet Name" name="pet_name" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Owner</label>
                      <?php
                        $owner_query = mysqli_query($con, "SELECT CONCAT(firstname, ' ', lastname) as full_name FROM patient WHERE id = '".$_SESSION['patient_id']."'");
                        $owner_data = mysqli_fetch_array($owner_query);
                      ?>
                      <input type="text" class="form-control" value="<?php echo $owner_data['full_name']; ?> (You)" readonly>
                      <input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Species</label>
                      <select name="species" class="form-control" required>
                        <option value="">Select Species</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Breed</label>
                      <input type="text" class="form-control" placeholder="Breed (optional)" name="breed">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Birth Date</label>
                      <input type="date" class="form-control" name="birth_date" id="birth_date">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Weight (lbs)</label>
                      <input type="number" step="0.01" class="form-control" placeholder="Weight" name="weight" min="0">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Sex</label>
                      <select name="sex" class="form-control">
                        <option value="U">Unknown</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                      </select>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" name="submit" class="btn btn-primary">Register Pet</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Pet Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Pet Information</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST">
                <input type="hidden" name="pet_id" id="edit_pet_id">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Pet Name</label>
                      <input type="text" class="form-control" placeholder="Pet Name" name="edit_pet_name" id="edit_pet_name" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Owner</label>
                      <?php
                        $owner_query = mysqli_query($con, "SELECT CONCAT(firstname, ' ', lastname) as full_name FROM patient WHERE id = '".$_SESSION['patient_id']."'");
                        $owner_data = mysqli_fetch_array($owner_query);
                      ?>
                      <input type="text" class="form-control" value="<?php echo $owner_data['full_name']; ?> (You)" readonly>
                      <input type="hidden" name="edit_patient_id" id="edit_patient_id" value="<?php echo $_SESSION['patient_id']; ?>">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Species</label>
                      <select name="edit_species" class="form-control" id="edit_species" required>
                        <option value="">Select Species</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Breed</label>
                      <input type="text" class="form-control" placeholder="Breed" name="edit_breed" id="edit_breed">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Birth Date</label>
                      <input type="date" class="form-control" name="edit_birth_date" id="edit_birth_date">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Weight (lbs)</label>
                      <input type="number" step="0.01" class="form-control" placeholder="Weight" name="edit_weight" id="edit_weight" min="0">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Sex</label>
                      <select name="edit_sex" class="form-control" id="edit_sex">
                        <option value="U">Unknown</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select name="edit_active" class="form-control" id="edit_active" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                      </select>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" name="update" class="btn btn-warning">Update Pet</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Delete Pet Record</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this pet's record?</p>
              <p><strong>Pet Name: <span id="delete_pet_name"></span></strong></p>
              <p class="text-danger"><small>This action cannot be undone.</small></p>
              <form method="POST">
                <input type="hidden" name="delete_pet_id" id="delete_pet_id">
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" name="delete" class="btn btn-danger">Delete Pet</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <?php include('../include/footer.php'); ?>
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
  
  <!-- JS Libraries -->
  <script src="../assets/modules/datatables/datatables.min.js"></script>
  <script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
  <script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/js/page/modules-datatables.js"></script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script>
    function editPet(id, name, species, breed, weight, sex, birth_date, patient_id, active) {
        document.getElementById('edit_pet_id').value = id;
        document.getElementById('edit_pet_name').value = name;
        document.getElementById('edit_species').value = species;
        document.getElementById('edit_breed').value = breed || '';
        document.getElementById('edit_weight').value = weight || '';
        document.getElementById('edit_sex').value = sex;
        document.getElementById('edit_birth_date').value = birth_date || '';
        document.getElementById('edit_active').value = active;
    }

    function deletePet(id, name) {
        document.getElementById('delete_pet_id').value = id;
        document.getElementById('delete_pet_name').textContent = name;
    }
  </script>
</body>
</html>