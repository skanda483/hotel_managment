<?php
include 'db_connect.php';

if (!isset($_POST['order_id'], $_POST['table_no'], $_POST['order_date'])) {
    die("Error: Required parameters are missing.");
}

$order_id = intval($_POST['order_id']);
$table_no = intval($_POST['table_no']);
$order_date = $_POST['order_date'];

$query = "
    SELECT 
        o.id AS order_id, o.room_no, o.order_date, 
        SUM(o.quantity * m.price) AS subtotal
    FROM orders o
    JOIN menu_items m ON o.dish_id = m.id
    WHERE o.table_number = ? AND o.order_date = ?
    GROUP BY o.table_number, o.order_date
";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $table_no, $order_date);
$stmt->execute();
$result = $stmt->get_result();
$order_data = $result->fetch_assoc();

if (!$order_data) {
    die("Error: No orders found for this table.");
}

$subtotal = $order_data['subtotal'];
$cgst = round($subtotal * 0.09, 2);
$sgst = round($subtotal * 0.09, 2);
$total = round($subtotal + $cgst + $sgst, 2);

$insertQuery = "
    INSERT INTO bill (order_id, table_no, room_no, subtotal, cgst, sgst, total, status, order_date) 
    SELECT ?, ?, ?, ?, ?, ?, ?, 'Not Cleared', ?
    FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM bill WHERE order_id = ?)
";

$stmt = $conn->prepare($insertQuery);
$stmt->bind_param(
    "iiidddssi",
    $order_id, $table_no, $order_data['room_no'], 
    $subtotal, $cgst, $sgst, $total, $order_date, $order_id
);
$stmt->execute();

$fetchBillQuery = "SELECT * FROM bill WHERE table_no = ? AND order_date = ? AND status = 'Not Cleared'";
$stmt = $conn->prepare($fetchBillQuery);
$stmt->bind_param("is", $table_no, $order_date);
$stmt->execute();
$billResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Details</title>
</head>
<body>

<h2>Billing Details</h2>

<?php if ($billResult->num_rows > 0): ?>
    <table border="1" id="billingTable">
        <tr>
            <th>Order ID</th>
            <th>Table No</th>
            <th>Subtotal (₹)</th>
            <th>CGST (9%)</th>
            <th>SGST (9%)</th>
            <th>Total (₹)</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($billData = $billResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $billData['order_id']; ?></td>
                <td><?php echo $billData['table_no']; ?></td>
                <td><?php echo number_format($billData['subtotal'], 2); ?></td>
                <td><?php echo number_format($billData['cgst'], 2); ?></td>
                <td><?php echo number_format($billData['sgst'], 2); ?></td>
                <td><?php echo number_format($billData['total'], 2); ?></td>
                <td><?php echo $billData['status']; ?></td>
                <td>
                    <button onclick="clearBill(<?php echo $billData['order_id']; ?>)">Clear Bill</button>
                    <button onclick="getBill(<?php echo $billData['order_id']; ?>)">Get Bill</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <h3>No pending bills found.</h3>
<?php endif; ?>

<script>
    function clearBill(orderId) {
        if (confirm("Are you sure you want to clear this bill?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "clear_bill.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    console.log("Server Response:", xhr.responseText); 

                    if (xhr.status === 200) {
                        if (xhr.responseText.trim() === "success") {
                            alert("Bill cleared successfully!");
                            location.reload(); 
                        } else if (xhr.responseText.trim() === "already_cleared") {
                            alert("Bill is already cleared.");
                        } else {
                            alert("Error clearing bill. Try again.");
                        }
                    } else {
                        alert("Server error. Please check logs.");
                    }
                }
            };

            xhr.send("order_id=" + orderId);
        }
    }

    function getBill(orderId) {
        window.location.href = "generate_bill.php?order_id=" + orderId;
    }
</script>

</body>
</html>
