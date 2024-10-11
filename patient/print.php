<?php
include('../include/patient_session.php');
$reservation_id = $_GET['id'];
$patient_id = $_SESSION['patient_id'];

$query = mysqli_query($con, "SELECT *, laboratory.id as laboratory_id FROM reservation INNER JOIN laboratory ON laboratory.id=reservation.laboratory_id 
                            INNER JOIN patient ON patient.id=reservation.patient_id WHERE reservation.patient_id = $patient_id AND reservation.id = $reservation_id");
$row = mysqli_fetch_array($query);
// Example variables (these could come from a database or form submission)
$patientName = $row['firstname'].' '. $row['lastname'];
$doctorName = "Dr. Jane Smith";
$visitDate = $row['tdate']; // Current date
$treatment = $row['laboratory_name'];
$time = $row['time'];
$laboratory_id = $row['laboratory_id'];
$cost = number_format($row['price'], 2); // The cost of the treatment
$paid = number_format($row['price'], 2); // Amount paid by the patient
$receiptNumber = $row['reference']; // Unique receipt number

// Optional: You could add more details like address, phone number, etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .receipt-details {
            margin-bottom: 20px;
        }
        .receipt-details p {
            margin: 5px 0;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <h1>Reservation Receipt</h1>
        <div class="receipt-header">
            <div>
                <p><strong>Patient:</strong> <?php echo $patientName; ?></p>
                
                <p><strong>Number:</strong> 
                        <?php
                            $query = mysqli_query($con, "SELECT COUNT(id) as max_id FROM reservation where laboratory_id = '$laboratory_id' AND tdate = '$visitDate'");
                            $row = mysqli_fetch_array($query);
                            echo $row['max_id'];
                        ?>
                </p>
            </div>
            <div>
                <p><strong>Date:</strong> <?php echo $visitDate; ?> <?php  echo $time; ?> </p>
                <p><strong>Receipt #:</strong> <?php echo $receiptNumber; ?></p>
            </div>
        </div>

        <div class="receipt-details">
            <p><strong>Treatment:</strong> <?php echo $treatment; ?></p>
            <p><strong>Cost:</strong> &#8369; <?php echo $cost; ?></p>
        </div>
        <hr style="color: #4CAF50">
        <div class="total">
            <p>Total Amount: &#8369; <?php echo $paid; ?></p>
        </div>

        <div class="footer">
            <p>Thank you for your visit!</p>
            <p>For more details, contact us at 123-456-7890</p>
        </div>
    </div>
        <script>
            window.print();
        </script>
</body>
</html>