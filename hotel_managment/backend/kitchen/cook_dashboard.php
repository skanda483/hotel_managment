<?php


// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure cook is logged in


// Add Inventory Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, unit) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $item_name, $quantity, $unit);

    if ($stmt->execute()) {
        echo "<script>alert('Item added successfully!'); window.location='cook_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding item: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch Inventory
$inventory = $conn->query("SELECT * FROM inventory");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Dashboard</title>
</head>
<body>

<h1>Welcome, Cook</h1>

<h2>Manage Inventory</h2>
<table border="1">
    <tr>
        <th>Item ID</th>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Date Added</th>
    </tr>
    <?php while ($row = $inventory->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['unit'] ?></td>
            <td><?= $row['added_date'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Add Inventory Item</h2>
<form method="post">
    <input type="text" name="item_name" placeholder="Item Name" required>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="text" name="unit" placeholder="Unit (e.g., kg, liter)" required>
    <button type="submit" name="add_item">Add Item</button>
</form>

<p><a href="logout.php">Logout</a></p>

</body>
</html>
