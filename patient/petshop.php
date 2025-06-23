<?php 
// Include database connection
include('../include/connection.php');
// Include patient session management
include('../include/patient_session.php'); 

// Get current patient ID from session
$current_patient_id = $_SESSION['patient_id'] ?? null;

if (!$current_patient_id) {
    header('Location: ../auth/patient_login.php');
    exit();
}

// Initialize messages variables
$success = null;
$error = null;

// Get species filter from GET parameter
$species_filter = $_GET['species'] ?? 'all';

// Handle Save Cart
if (isset($_POST['save_cart'])) {
    $patient_id = $_SESSION['patient_id'];
    $cart_data = json_encode($_SESSION['cart']); // Convert cart to JSON

    // Insert or update the saved cart
    $query = mysqli_query($con, "INSERT INTO saved_carts (patient_id, cart_data) VALUES ($patient_id, '$cart_data') ON DUPLICATE KEY UPDATE cart_data = '$cart_data'");
}

// Load saved cart on login
$saved_cart_query = mysqli_query($con, "SELECT cart_data FROM saved_carts WHERE patient_id = $current_patient_id LIMIT 1");
if ($saved_cart = mysqli_fetch_assoc($saved_cart_query)) {
    $_SESSION['cart'] = json_decode($saved_cart['cart_data'], true);
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Get item details
    $query = mysqli_query($con, "SELECT i.*, c.category_name 
        FROM inventory i 
        JOIN item_category c ON i.category_id = c.id 
        WHERE i.id = $item_id AND i.active = 1");
    
    if ($item = mysqli_fetch_assoc($query)) {
        $current_qty = $_SESSION['cart'][$item_id]['quantity'] ?? 0;
        
        if (($current_qty + $quantity) <= $item['quantity']) {
            $_SESSION['cart'][$item_id] = [
                'name' => $item['item_name'],
                'price' => $item['price'],
                'category' => $item['category_name'],
                'quantity' => $current_qty + $quantity
            ];
        }
    }
}

// Handle Remove from Cart
if (isset($_POST['remove_cart'])) {
    $item_id_to_remove = $_POST['item_id'];
    if (isset($_SESSION['cart'][$item_id_to_remove])) {
        unset($_SESSION['cart'][$item_id_to_remove]);

        // Update the saved cart in the database to reflect this change
        $patient_id = $_SESSION['patient_id'];
        $updated_cart_data = json_encode($_SESSION['cart']);

        // If cart is now empty, optionally remove the saved cart record
        if (empty($_SESSION['cart'])) {
            mysqli_query($con, "DELETE FROM saved_carts WHERE patient_id = $patient_id");
        } else {
            // Otherwise update the saved cart data
            mysqli_query($con, "UPDATE saved_carts SET cart_data = '$updated_cart_data' WHERE patient_id = $patient_id");
        }
    }
}

// Handle finalize checkout with payment method
if (isset($_POST['finalize_checkout']) && !empty($_SESSION['cart'])) {
    $mop = $_POST['mop'] ?? null; // Get the selected payment method
    // Validate payment method
    if (!in_array($mop, ['1', '2'])) { // Assuming 1 is Cash and 2 is PayPal
        $error = "Invalid payment method selected.";
    } else {
        $patient_id = $_SESSION['patient_id'];
        $order_number = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $total_amount = 0;
        $total_items = 0;
        
        // Calculate totals
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
            $total_items += $item['quantity'];
        }
        
        // Insert order with payment method and status
        $order_status = "pending";
        $payment_status = "unpaid";

        // Insert order
        $insert_order_sql = "INSERT INTO orders (patient_id, order_number, total_amount, total_items, order_status, mop, payment_status) 
            VALUES ($patient_id, '$order_number', $total_amount, $total_items, '$order_status', '$mop', '$payment_status')";
        $insert_order = mysqli_query($con, $insert_order_sql);
        
        if ($insert_order) {
            $order_id = mysqli_insert_id($con);
            
            // Insert order items and update inventory
            foreach ($_SESSION['cart'] as $id => $item) {
                // Insert order item
                mysqli_query($con, "INSERT INTO order_items (order_id, inventory_id, item_name, category_name, quantity, unit_price, total_price) 
                    VALUES ($order_id, $id, '{$item['name']}', '{$item['category']}', {$item['quantity']}, {$item['price']}, " . ($item['price'] * $item['quantity']) . ")");
                
                // Update inventory
                mysqli_query($con, "UPDATE inventory SET quantity = quantity - {$item['quantity']} WHERE id = $id");
            }
            
            // Delete saved cart after successful checkout
            mysqli_query($con, "DELETE FROM saved_carts WHERE patient_id = $patient_id");
            
            $_SESSION['checkout_success'] = [
                'order_number' => $order_number,
                'total_amount' => $total_amount,
                'total_items' => $total_items,
                'mop' => $mop
            ];
            $_SESSION['cart'] = [];

            // Redirect to clear POST and show success msg
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Failed to place the order.";
        }
    }
}

// Clear checkout success message when requested
if (isset($_GET['clear_success'])) {
    unset($_SESSION['checkout_success']);
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

  <!-- PayPal SDK -->
  <script src="https://www.paypal.com/sdk/js?client-id=AaQK2c3sE-7O-kRJnsvXZ-toVwFn59XKAN_20kutjnSKCnWDd1ukV20a0kEepSRorskGHvLEFkTVeyZE&currency=PHP&components=buttons&enable-funding=venmo"></script>

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
            <h1>Pet Store</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Shop</a></div>
              <div class="breadcrumb-item">Pet Store</div>
            </div>
          </div>

          <div class="section-body">
            
            <?php if ($success): ?>
              <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Checkout Success Summary -->
            <?php if (isset($_SESSION['checkout_success'])): ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body text-center" style="background: linear-gradient(45deg, #28a745, #20c997); color: white; border-radius: 10px;">
                    <h2><i class="fas fa-check-circle"></i> Order Placed Successfully!</h2>
                    <p class="mb-2">Order Number: <strong><?php echo htmlspecialchars($_SESSION['checkout_success']['order_number']); ?></strong></p>
                    <p class="mb-2">Total Amount: <strong>₱<?php echo number_format($_SESSION['checkout_success']['total_amount'], 2); ?></strong></p>
                    <p class="mb-3">Payment Method: <strong><?php echo $_SESSION['checkout_success']['mop'] == '1' ? 'Cash' : 'PayPal'; ?></strong></p>
                    <a href="?clear_success=1" class="btn btn-light btn-lg">Continue Shopping</a>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if (!isset($_SESSION['checkout_success'])): ?>
            <h2 class="section-title">Available Products</h2>
            
            <!-- Species Filter -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4><i class="fas fa-filter"></i> Filter by Species</h4>
                  </div>
                  <div class="card-body">
                    <form method="GET" class="form-inline">
                      <div class="form-group mr-3">
                        <label for="species" class="mr-2"><strong>Species:</strong></label>
                        <select name="species" id="species" class="form-control" onchange="this.form.submit()">
                          <option value="all" <?php echo $species_filter == 'all' ? 'selected' : ''; ?>>All Products</option>
                          <option value="dog" <?php echo $species_filter == 'dog' ? 'selected' : ''; ?>>Dog Only</option>
                          <option value="cat" <?php echo $species_filter == 'cat' ? 'selected' : ''; ?>>Cat Only</option>
                          <option value="both" <?php echo $species_filter == 'both' ? 'selected' : ''; ?>>Both (Dog & Cat)</option>
                        </select>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Products Table 
                      <?php if ($species_filter != 'all'): ?>
                        <span class="badge badge-info ml-2">
                          Filtered by: <?php echo ucfirst($species_filter == 'both' ? 'Both Species' : $species_filter); ?>
                        </span>
                      <?php endif; ?>
                    </h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Available Stock</th>
                            <th>Details</th>
                            <th>Quantity</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            // Build the WHERE clause based on species filter
                            $species_condition = "";
                            if ($species_filter == 'dog') {
                                $species_condition = "AND (i.species = 'dog' OR i.species LIKE '%dog%')";
                            } elseif ($species_filter == 'cat') {
                                $species_condition = "AND (i.species = 'cat' OR i.species LIKE '%cat%')";
                            } elseif ($species_filter == 'both') {
                                $species_condition = "AND (i.species = 'both' OR i.species LIKE '%both%')";
                            }
                            
                            $products = mysqli_query($con, "SELECT i.*, c.category_name 
                                FROM inventory i 
                                JOIN item_category c ON i.category_id = c.id 
                                WHERE i.active = 1 AND i.quantity > 0 $species_condition
                                ORDER BY c.category_name, i.item_name");
                            
                            $count = 0;
                            while ($product = mysqli_fetch_assoc($products)):
                                $in_cart = $_SESSION['cart'][$product['id']]['quantity'] ?? 0;
                                $available = $product['quantity'] - $in_cart;
                                $count++;
                          ?>
                          <tr>
                            <td><?php echo $count; ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($product['category_name']); ?></span></td>
                            <td><?php echo htmlspecialchars($product['item_name']); ?></td>
                            <td><span style="font-size: 16px; font-weight: bold;">₱<?php echo number_format($product['price'], 2); ?></span></td>
                            <td>
                              <span class="<?php echo $available > 0 ? 'text-dark' : 'text-danger'; ?>">
                                <?php echo $available; ?>
                              </span>
                            </td>
                            <td>
                              <?php if (!empty($product['details'])): ?>
                              <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailsModal" 
                                      onclick="showDetails('<?php echo htmlspecialchars($product['item_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($product['details'], ENT_QUOTES); ?>')">
                                <i class="fas fa-info-circle"></i> View
                              </button>
                              <?php else: ?>
                              <span class="text-muted">No details</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if ($available > 0): ?>
                              <form method="POST" class="d-inline-flex align-items-center">
                                <input type="hidden" name="item_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" min="1" max="<?php echo $available; ?>" value="1" class="form-control form-control-sm mr-2" style="width:80px;">
                                <button type="submit" name="add_to_cart" class="btn btn-sm btn-success">
                                  <i class="fas fa-cart-plus"></i> Add
                                </button>
                              </form>
                              <?php else: ?>
                              <span class="badge badge-secondary">Out of Stock</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Shopping Cart Section -->
            <?php if (!empty($_SESSION['cart'])): ?>
            <h2 class="section-title">Shopping Cart</h2>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Cart Items</h4>
                    <div class="card-header-action">
                      <form method="POST" style="display: inline;">
                        <button type="submit" name="save_cart" class="btn btn-primary">
                          <i class="fas fa-save"></i> Save Cart
                        </button>
                      </form>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $cart_total = 0;
                          $item_count = 0;
                          foreach ($_SESSION['cart'] as $id => $item):
                              $item_total = $item['price'] * $item['quantity'];
                              $cart_total += $item_total;
                              $item_count++;
                          ?>
                          <tr>
                            <td><?php echo $item_count; ?></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₱<?php echo number_format($item_total, 2); ?></td>
                            <td>
                              <form method="POST" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                <button type="submit" name="remove_cart" class="btn btn-sm btn-danger">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </form>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                          <tr class="table-success">
                            <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                            <td><strong>₱<?php echo number_format($cart_total, 2); ?></strong></td>
                            <td>
                              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#paymentModal">
                                <i class="fas fa-credit-card"></i> Checkout
                              </button>
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
          </div>
        </section>
      </div>

      <!-- Product Details Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="detailsModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="detailsModalTitle">Product Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="detailsModalContent">
                <!-- Details content will be loaded here -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="paymentModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Select Payment Method</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label>Payment Method</label>
                    <select name="mop" class="form-control" id="mop" required>
                      <option value="#" disabled selected>Choose...</option>
                      <option value="1">Cash</option>
                      <option value="2">PayPal</option>
                    </select>
                  </div>
                </div>
              </div>
              <div id="paypal-button-container" style="display: none"></div>
            </div>
            <div class="modal-footer bg-whitesmoke br" id="modalfooter">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="finalize_checkout" value="1">
                <input type="hidden" name="mop" id="selected_mop" value="">
                <button type="submit" class="btn btn-success" id="confirm_payment">Confirm Payment</button>
              </form>
            </div>
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
    // Function to show product details in modal
    function showDetails(itemName, details) {
        $('#detailsModalTitle').text(itemName + ' - Details');
        $('#detailsModalContent').html('<p>' + details.replace(/\n/g, '<br>') + '</p>');
    }

    // Payment method selection handling
    $('#mop').on('change', function() {
        var mop = $('#mop').val();
        $('#selected_mop').val(mop);
        
        if(mop == '1') {
            $('#modalfooter').css('display', 'block');
            $('#paypal-button-container').css('display', 'none');
        } else if(mop == '2') {
            $('#paypal-button-container').css('display', 'block');
            $('#modalfooter').css('display', 'none');
        }
    });

    // PayPal integration
    <?php if (!empty($_SESSION['cart'])): ?>
    var totalPrice = <?php echo $cart_total ?? 0; ?>;
    paypal.Buttons({
      createOrder: function(data, actions) {
        return actions.order.create({
            "purchase_units": [{
                "amount": {
                    "currency_code": "PHP",
                    "value": parseFloat(totalPrice),
                },
            }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Create form data for PayPal transaction
            var formData = new FormData();
            formData.append('finalize_checkout', '1');
            formData.append('mop', '2');
            
            // Submit the form
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            var input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'finalize_checkout';
            input1.value = '1';
            form.appendChild(input1);
            
            var input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'mop';
            input2.value = '2';
            form.appendChild(input2);
            
            document.body.appendChild(form);
            form.submit();
        });
      }
    }).render('#paypal-button-container');
    <?php endif; ?>
  </script>
</body>
</html>