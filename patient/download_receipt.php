<?php 
include('../include/patient_session.php');

if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $patient_id = $_SESSION['patient_id'];
    
    // Get order details (removed p.phone from SELECT)
    $order_query = mysqli_query($con, "SELECT 
        o.id, 
        o.order_number, 
        o.order_date, 
        o.completed_date,
        o.total_items, 
        o.total_amount, 
        o.order_status, 
        o.payment_status,
        o.mop,
        o.notes,
        p.firstname,
        p.lastname,
        p.email
      FROM orders o 
      INNER JOIN patient p ON p.id = o.patient_id
      WHERE o.id = $order_id AND o.patient_id = $patient_id AND o.order_status = 'completed'");
    
    if(mysqli_num_rows($order_query) > 0) {
        $order = mysqli_fetch_array($order_query);
        
        // Get order items
        $items_query = mysqli_query($con, "SELECT 
            oi.item_name,
            oi.category_name,
            oi.quantity,
            oi.unit_price,
            oi.total_price,
            oi.item_details
          FROM order_items oi 
          WHERE oi.order_id = $order_id
          ORDER BY oi.item_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo $order['order_number']; ?></title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="css/order-receipt.css">
    
    <!-- External JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <div class="no-print action-buttons">
        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>
        <button class="btn btn-success" onclick="window.print()">Print Receipt</button>
        <button class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    <div id="receipt-content">
        <div class="receipt-header">
            <div class="receipt-title">ORDER RECEIPT</div>
            <div>Shepherd Animal Clinic</div>
            <div>Thank you for your purchase!</div>
        </div>

        <div class="order-info">
            <div class="customer-info">
                <div class="info-section">
                    <div class="info-label">Customer Information:</div>
                    <div><?php echo $order['firstname'] . ' ' . $order['lastname']; ?></div>
                    <div><?php echo $order['email']; ?></div>
                </div>
            </div>
            
            <div class="order-details">
                <div class="info-section">
                    <div class="info-label">Order Details:</div>
                    <div><strong>Order #:</strong> <?php echo $order['order_number']; ?></div>
                    <div><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></div>
                    <div><strong>Completed:</strong> <?php echo date('M d, Y h:i A', strtotime($order['completed_date'])); ?></div>
                    <div><strong>Payment Method:</strong> <?php echo $order['mop'] ? $order['mop'] : 'N/A'; ?></div>
                    <div><strong>Status:</strong> <?php echo strtoupper($order['order_status']); ?></div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                while($item = mysqli_fetch_array($items_query)) { 
                    $subtotal += $item['total_price'];
                ?>
                <tr>
                    <td><?php echo $item['item_name']; ?></td>
                    <td><?php echo $item['category_name']; ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-right">₱<?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="text-right">₱<?php echo number_format($item['total_price'], 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="4" class="text-right">Subtotal:</th>
                    <th class="text-right">₱<?php echo number_format($subtotal, 2); ?></th>
                </tr>
                <tr class="total-row">
                    <th colspan="4" class="text-right">TOTAL AMOUNT:</th>
                    <th class="text-right">₱<?php echo number_format($order['total_amount'], 2); ?></th>
                </tr>
            </tfoot>
        </table>

        <?php if($order['notes']) { ?>
        <div class="info-section">
            <div class="info-label">Notes:</div>
            <div><?php echo nl2br(htmlspecialchars($order['notes'])); ?></div>
        </div>
        <?php } ?>

        <div class="receipt-footer">
            <p>This is a computer-generated receipt.</p>
            <p>Generated on: <?php echo date('M d, Y h:i A'); ?></p>
            <p>Thank you for choosing our services!</p>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="js/order-receipt.js"></script>
    
    <!-- Set order number for JavaScript -->
    <script>
        window.orderNumber = '<?php echo $order['order_number']; ?>';
    </script>
</body>
</html>

<?php
    } else {
        echo '<div style="text-align: center; margin-top: 50px;">';
        echo '<h3>Receipt not found or access denied.</h3>';
        echo '<p>Only completed orders can generate receipts.</p>';
        echo '<button onclick="window.close()">Close</button>';
        echo '</div>';
    }
} else {
    echo '<div style="text-align: center; margin-top: 50px;">';
    echo '<h3>Invalid request.</h3>';
    echo '<button onclick="window.close()">Close</button>';
    echo '</div>';
}
?>