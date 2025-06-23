<?php
include('../include/cashier_session.php');
// Query to fetch pending orders with patient details (adjust field names accordingly)
$query = mysqli_query($con, "
  SELECT o.id, o.patient_id, o.order_date, o.status, p.name AS patient_name,
  SUM(oi.total_price) AS total_price
  FROM orders o
  JOIN patients p ON o.patient_id = p.id
  JOIN order_items oi ON oi.order_id = o.id
  WHERE o.status = 'Pending'
  GROUP BY o.id
");

// Button triggers AJAX call to update status to Paid
