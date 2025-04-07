<?php
include 'db_connect.php';

if (!isset($_POST['order_id'])) {
    die("Error: Order ID is missing.");
}

$order_id = intval($_POST['order_id']);

file_put_contents("debug_log.txt", "clear_bill.php accessed\nReceived order_id: $order_id\n", FILE_APPEND);

$checkQuery = "SELECT * FROM bill WHERE order_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$billData = $result->fetch_assoc();

if (!$billData) {
    file_put_contents("debug_log.txt", "No matching bill found for order_id: $order_id\n", FILE_APPEND);
    die("Error: No matching bill found.");
}

if ($billData['status'] === 'Cleared') {
    echo "Bill is already cleared.";
    exit;
}

$query = "UPDATE bill SET status = 'Cleared' WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success";
} else {
    die("Error clearing bill. Try again.");
}
?>