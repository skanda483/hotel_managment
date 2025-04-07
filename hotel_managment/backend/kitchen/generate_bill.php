<?php
include 'db_connect.php';

$query = "
    SELECT 
        orders.table_number, 
        orders.order_date 
    FROM orders 
    WHERE status = 'Completed'
    ORDER BY orders.order_date DESC
    LIMIT 1
";

$result = $conn->query($query);
if ($result->num_rows === 0) {
    die("<h3>Error: No completed orders found!</h3>");
}

$row = $result->fetch_assoc();
$table_no = $row['table_number'];
$order_date = $row['order_date'];

$query = "
    SELECT 
        menu_items.item_name, 
        orders.quantity, 
        (menu_items.price * orders.quantity) AS price
    FROM orders
    JOIN menu_items ON orders.dish_id = menu_items.id
    WHERE orders.table_number = ? AND orders.order_date = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $table_no, $order_date);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$subtotal = 0;
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $subtotal += $row['price'];
}

$cgst = $subtotal * 0.09;
$sgst = $subtotal * 0.09;
$total = $subtotal + $cgst + $sgst;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
        .bill-container { width: 300px; margin: auto; border: 1px solid #000; padding: 15px; background: #f8f8f8; }
        .bill-header { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 10px; }
        .bill-table { width: 100%; }
        .bill-table td { padding: 5px; }
        .total { font-weight: bold; }
        .print-btn { margin-top: 10px; padding: 10px; background: green; color: white; border: none; cursor: pointer; }
        .print-btn:hover { background: darkgreen; }
    </style>
</head>
<body>

<div class="bill-container">
    <div class="bill-header">Restaurant Bill</div>
    <table class="bill-table">
        <tr><td>Table No:</td><td><?php echo htmlspecialchars($table_no); ?></td></tr>
        <tr><td>Order Date:</td><td><?php echo htmlspecialchars($order_date); ?></td></tr>
    </table>

    <h3>Items</h3>
    <table class="bill-table">
        <?php foreach ($items as $item) { ?>
            <tr>
                <td><?php echo $item['item_name']; ?> x<?php echo $item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['price'], 2); ?></td>
            </tr>
        <?php } ?>
        <tr><td>Subtotal:</td><td>₹<?php echo number_format($subtotal, 2); ?></td></tr>
        <tr><td>CGST (9%):</td><td>₹<?php echo number_format($cgst, 2); ?></td></tr>
        <tr><td>SGST (9%):</td><td>₹<?php echo number_format($sgst, 2); ?></td></tr>
        <tr><td class="total">Total:</td><td class="total">₹<?php echo number_format($total, 2); ?></td></tr>
    </table>
    
    <p>Thank you for dining with us!</p>
    
    <button class="print-btn" onclick="window.print()">Print Bill</button>
</div>

</body>
</html>
