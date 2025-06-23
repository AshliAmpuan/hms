<?php include('../include/admin_session.php'); ?>
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
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Admin</a></div>
              <div class="breadcrumb-item">Inventory Management</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Item Inventory Management</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Items Table</h4>
                    <div class="card-header-action">
                      <button class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus"></i> Add Item</button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Item Name</th>
                            <th>Species</th>
                            <th>Quantity</th>
                            <th>Details</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = mysqli_query($con, "SELECT inventory.id, item_category.category_name, inventory.item_name, inventory.species, inventory.quantity, inventory.details, inventory.price, inventory.active, inventory.category_id 
                            FROM inventory 
                            INNER JOIN item_category ON item_category.id = inventory.category_id");
                            $count = 0;
                            while($row = mysqli_fetch_array($query)){
                              $count += 1;
                              $status = $row['active'] ? 'Active' : 'Not Active';
                              $statusClass = $row['active'] ? 'badge-success' : 'badge-danger';
                              
                              // Fixed species display logic
                              $species_display = '';
                              if (empty($row['species'])) {
                                  $species_display = '-';
                              } elseif ($row['species'] == 'Both') {
                                  $species_display = 'Dog and Cat';
                              } else {
                                  $species_display = $row['species'];
                              }
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($species_display); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($row['details']); ?></td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                            <td>
                              <button class="btn btn-sm btn-warning" onclick="editItem(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['item_name'], ENT_QUOTES); ?>', <?php echo $row['category_id']; ?>, '<?php echo htmlspecialchars($row['species'], ENT_QUOTES); ?>', <?php echo $row['quantity']; ?>, '<?php echo htmlspecialchars($row['details'], ENT_QUOTES); ?>', <?php echo $row['price']; ?>, <?php echo $row['active']; ?>)" data-toggle="modal" data-target="#editModal">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['item_name'], ENT_QUOTES); ?>')" data-toggle="modal" data-target="#deleteModal">
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

      <!-- Add Item Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add Item</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Item Name</label>
                      <input type="text" class="form-control" placeholder="Item Name" name="item_name" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Category</label>
                      <select name="category" class="form-control" id="category" required>
                        <option value="" disabled selected>Choose...</option>
                        <?php
                          $query = mysqli_query($con, "SELECT * FROM item_category");
                          while($row = mysqli_fetch_array($query)){
                        ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Species</label>
                      <select name="species" class="form-control">
                        <option value="">Choose...</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Both">Dog and Cat</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Quantity</label>
                      <input type="number" class="form-control" placeholder="Quantity" name="quantity" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Price</label>
                      <input type="number" step="0.01" class="form-control" placeholder="Price" name="price" required>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Details</label>
                      <textarea name="details" class="form-control" rows="4"></textarea>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Item Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Item</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Item Name</label>
                      <input type="text" class="form-control" placeholder="Item Name" name="edit_item_name" id="edit_item_name" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Category</label>
                      <select name="edit_category" class="form-control" id="edit_category" required>
                        <option value="" disabled>Choose...</option>
                        <?php
                          $query = mysqli_query($con, "SELECT * FROM item_category");
                          while($row = mysqli_fetch_array($query)){
                        ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Species</label>
                      <select name="edit_species" class="form-control" id="edit_species">
                        <option value="">Choose...</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Both">Dog and Cat</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Quantity</label>
                      <input type="number" class="form-control" placeholder="Quantity" name="edit_quantity" id="edit_quantity" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Price</label>
                      <input type="number" step="0.01" class="form-control" placeholder="Price" name="edit_price" id="edit_price" required>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select name="edit_active" class="form-control" id="edit_active" required>
                        <option value="1">Active</option>
                        <option value="0">Not Active</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Details</label>
                      <textarea name="edit_details" class="form-control" rows="4" id="edit_details"></textarea>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" name="update" class="btn btn-warning">Update Item</button>
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
              <h5 class="modal-title">Delete Item</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this item?</p>
              <p><strong>Item Name: <span id="delete_item_name"></span></strong></p>
              <form method="POST">
                <input type="hidden" name="delete_item_id" id="delete_item_id">
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" name="delete" class="btn btn-danger">Delete Item</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <?php include('../include/footer.php'); ?>
    </div>
  </div>

  <?php
    // Add Item - Using prepared statements for security
    if(isset($_POST['submit'])) {
        $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
        $category = mysqli_real_escape_string($con, $_POST['category']);
        $species = mysqli_real_escape_string($con, $_POST['species']);
        $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
        $details = mysqli_real_escape_string($con, $_POST['details']);
        $price = mysqli_real_escape_string($con, $_POST['price']);

        // Ensure species is stored correctly - empty string if not selected
        if (empty($species)) {
            $species = '';
        }

        $insertQuery = mysqli_query($con, "INSERT INTO inventory (category_id, item_name, species, quantity, details, price, active) VALUES ('$category', '$item_name', '$species', '$quantity', '$details', '$price', 1)");
        if($insertQuery) {
            echo "<script>alert('Item added successfully!')</script>";
            echo "<script>location.replace('inventory.php')</script>";
        } else {
            echo "<script>alert('Something went wrong! Error: " . mysqli_error($con) . "')</script>";
        }
    }

    // Update Item
    if(isset($_POST['update'])) {
        $item_id = mysqli_real_escape_string($con, $_POST['item_id']);
        $item_name = mysqli_real_escape_string($con, $_POST['edit_item_name']);
        $category = mysqli_real_escape_string($con, $_POST['edit_category']);
        $species = mysqli_real_escape_string($con, $_POST['edit_species']);
        $quantity = mysqli_real_escape_string($con, $_POST['edit_quantity']);
        $details = mysqli_real_escape_string($con, $_POST['edit_details']);
        $price = mysqli_real_escape_string($con, $_POST['edit_price']);
        $active = mysqli_real_escape_string($con, $_POST['edit_active']);

        // Ensure species is stored correctly - empty string if not selected
        if (empty($species)) {
            $species = '';
        }

        $updateQuery = mysqli_query($con, "UPDATE inventory SET category_id='$category', item_name='$item_name', species='$species', quantity='$quantity', details='$details', price='$price', active='$active' WHERE id='$item_id'");
        if($updateQuery) {
            echo "<script>alert('Item updated successfully!')</script>";
            echo "<script>location.replace('inventory.php')</script>";
        } else {
            echo "<script>alert('Something went wrong! Error: " . mysqli_error($con) . "')</script>";
        }
    }

    // Delete Item
    if(isset($_POST['delete'])) {
        $item_id = mysqli_real_escape_string($con, $_POST['delete_item_id']);

        $deleteQuery = mysqli_query($con, "DELETE FROM inventory WHERE id='$item_id'");
        if($deleteQuery) {
            echo "<script>alert('Item deleted successfully!')</script>";
            echo "<script>location.replace('inventory.php')</script>";
        } else {
            echo "<script>alert('Something went wrong! Error: " . mysqli_error($con) . "')</script>";
        }
    }
  ?>

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
  
  <!-- Updated JavaScript for proper species handling -->
  <script src="../assets/js/admin-inventory.js"></script>
</body>
</html>