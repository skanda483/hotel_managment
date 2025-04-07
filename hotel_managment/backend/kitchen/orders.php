<?php
include 'db_connect.php';

$tables = $conn->query("SELECT * FROM tables WHERE status = 'Available'");



$menu = $conn->query("SELECT * FROM menu_items"); 
if (!$menu) {
    die("Error fetching menu items: " . $conn->error);
}


$orders = $conn->query("
    SELECT 
        MIN(orders.id) AS order_id,  -- Use MIN to ensure at least one ID is included
        orders.order_date, 
        orders.table_number, 
        GROUP_CONCAT(menu_items.item_name ORDER BY menu_items.item_name SEPARATOR ', ') AS dishes, 
        GROUP_CONCAT(orders.quantity ORDER BY menu_items.item_name SEPARATOR ', ') AS quantities, 
        orders.status 
    FROM orders
    JOIN menu_items ON orders.dish_id = menu_items.id
    GROUP BY orders.table_number, orders.order_date, orders.status
    ORDER BY orders.order_date DESC
");

if (isset($_POST['place_order'])) {
    $table_no = $_POST['table_no'];
    $room_no = $_POST['room_no'];
    $dish_ids = $_POST['dish_id'];
    $quantities = $_POST['quantity'];
    $order_date = date("Y-m-d H:i:s"); 

    if (empty($dish_ids) || empty($quantities)) {
        die("Error: Dish IDs or Quantities are missing.");
    }

    foreach ($dish_ids as $index => $dish_id) {
        $qty = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
        if ($qty <= 0) {
            die("Error: Invalid quantity detected.");
        }

        $stmt = $conn->prepare("
            INSERT INTO orders (table_number, room_no, dish_id, quantity, status, order_date) 
            VALUES (?, ?, ?, ?, 'Preparing', ?)
        ");
        $stmt->bind_param("iiiis", $table_no, $room_no, $dish_id, $qty, $order_date);
        $stmt->execute();
    }

    $conn->query("UPDATE tables SET status = 'Booked' WHERE table_no = $table_no");

    echo "<script>alert('Order placed successfully!'); window.location.href='orders.php';</script>";
}
?>

<h1>Waiter Dashboard</h1>
<h2>Place Order</h2>
<form method="POST">
    <label>Table No:</label>
    <select name="table_no" required>
        <?php while ($row = $tables->fetch_assoc()) { ?>
            <option value="<?php echo $row['table_no']; ?>">Table <?php echo $row['table_no']; ?></option>
        <?php } ?>
    </select>

    <label>Room No:</label>
    <input type="number" name="room_no" required>

    <div id="order_items">
        <div class="order_item">
            <label>Dish:</label>
            <select name="dish_id[]" required>
                <?php while ($row = $menu->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['item_name'] . " - ₹" . $row['price']; ?></option>
                <?php } ?>
            </select>

            <label>Quantity:</label>
            <input type="number" name="quantity[]" required>
        </div>
    </div>

    <button type="button" onclick="addOrderItem()">+ Add More Dish</button>
    <button type="submit" name="place_order">Submit</button>
</form>

<script>
    let menuItems = `<?php 
        $menu->data_seek(0); 
        while ($row = $menu->fetch_assoc()) { 
            echo "<option value='{$row['id']}'>{$row['item_name']} - ₹{$row['price']}</option>"; 
        } 
    ?>`;

    function addOrderItem() {
        let orderDiv = document.getElementById("order_items");
        let newItem = document.createElement("div");
        newItem.classList.add("order_item");
        newItem.innerHTML = `
            <label>Dish:</label>
            <select name="dish_id[]" required>${menuItems}</select>
            <label>Quantity:</label>
            <input type="number" name="quantity[]" required>
        `;
        orderDiv.appendChild(newItem);
    }
</script>


<h2>Order Status</h2>
<table border="1">
    <tr>
        <th>Table No</th>
        <th>Dishes</th>
        <th>Quantities</th>
        <th>Order Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $orders->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['table_number']; ?></td>
        <td><?php echo $row['dishes']; ?></td>
        <td><?php echo $row['quantities']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if ($row['status'] == 'Completed') { ?>
                <form method="POST" action="billing.php">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">

    <input type="hidden" name="table_no" value="<?php echo htmlspecialchars($row['table_number']); ?>">
    <input type="hidden" name="order_date" value="<?php echo htmlspecialchars($row['order_date']); ?>">
    <button type="submit" name="generate_bill">Generate Bill</button>
</form>



            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>
