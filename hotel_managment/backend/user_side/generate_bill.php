<?php
include 'db.php';

if (isset($_GET['booking_id']) && $_GET['type'] == "room") {
    $booking_id = $_GET['booking_id'];

    $sql = "SELECT * FROM room_bookings WHERE booking_id = $booking_id";
    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();
} else {
    echo "Invalid Request!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Bill</title>
</head>
<body>
    <h2>🧾 Room Booking Bill</h2>
    <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></p>
    <p><strong>Room Number:</strong> <?php echo $booking['room_number']; ?></p>
    <p><strong>Guests:</strong> <?php echo $booking['guests']; ?></p>
    <p><strong>Check-in:</strong> <?php echo $booking['check_in']; ?></p>
    <p><strong>Check-out:</strong> <?php echo $booking['check_out']; ?></p>
    <p><strong>Status:</strong> <?php echo $booking['status']; ?></p>
    <p><strong>Price:</strong> ₹<?php echo $booking['price']; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $booking['payment_status']; ?></p>

    <button onclick="window.print()">🖨 Print Bill</button><br><br>
    <a href="index.php">🏠 Home</a>
</body>
</html>
