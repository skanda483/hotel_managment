<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management";  

$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}


$checkTables = $conn->query("SHOW TABLES LIKE 'tables'");
if ($checkTables->num_rows == 0) {
    die("<p style='color:red;'>Error: The table 'tables' does not exist in your database.</p>");
}

$checkRooms = $conn->query("SHOW TABLES LIKE 'rooms'");
if ($checkRooms->num_rows == 0) {
    die("<p style='color:red;'>Error: The table 'rooms' does not exist in your database.</p>");
}

$tableQuery = "SELECT * FROM tables ORDER BY id DESC";
$tableResult = $conn->query($tableQuery);

$roomQuery = "SELECT * FROM rooms ORDER BY id DESC";
$roomResult = $conn->query($roomQuery);
?>

<h2>🍽 Restaurant Table Bookings</h2>
<table border="1">
    <tr><th>Table ID</th><th>Seats</th><th>Status</th><th>Bill</th></tr>
    <?php while ($row = $tableResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['seats']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><a href="generate_bill.php?booking_id=<?php echo $row['id']; ?>&type=table">🧾 Generate Bill</a></td>
        </tr>
    <?php } ?>
</table>

<h2>🏨 Hotel Room Bookings</h2>
<table border="1">
    <tr><th>Room ID</th><th>Type</th><th>Status</th><th>Bill</th></tr>
    <?php while ($row = $roomResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['type']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><a href="generate_bill.php?booking_id=<?php echo $row['id']; ?>&type=room">🧾 Generate Bill</a></td>
        </tr>
    <?php } ?>
</table>

<a href="index.php">🏠 Back to Home</a>
