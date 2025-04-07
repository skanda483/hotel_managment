<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management"; // Adjust if your database name is different

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch available tables
$availableTables = $conn->query("SELECT table_no FROM tables WHERE status = 'Available'");

if (isset($_POST['book_table'])) {
    $guest_name = $_POST['guest_name'];
    $phone = $_POST['phone'];
    $table_no = $_POST['table_no'];
    $time = $_POST['time'];

    if (empty($table_no)) {
        $message = "Please select an available table.";
    } else {
        $check_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check_user->bind_param("s", $guest_name);
        $check_user->execute();
        $result = $check_user->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO users (username, phone) VALUES (?, ?)");
            $stmt->bind_param("ss", $guest_name, $phone);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE username = ?");
            $stmt->bind_param("ss", $phone, $guest_name);
            $stmt->execute();
            $stmt->close();
        }
        $check_user->close();

       
$stmt = $conn->prepare("UPDATE tables SET status = 'Booked' WHERE table_no = ?");
$stmt->bind_param("i", $table_no);
$stmt->execute();
$stmt->close();


        $stmt = $conn->prepare("UPDATE tables SET status = 'Booked' WHERE table_no = ?");
        $stmt->bind_param("i", $table_no);
        $stmt->execute();
        $stmt->close();

        $message = "Table $table_no booked successfully!";
    }
}
?>

<h1>Book a Table</h1>
<?php if (isset($message)) { echo "<p><strong>$message</strong></p>"; } ?>

<form method="POST">
    Guest Name: <input type="text" name="guest_name" required>
    Phone: <input type="text" name="phone" required>

    <label for="table_no">Table Number:</label>
    <select name="table_no" required>
        <option value="">-- Select a Table --</option>
        <?php
        if ($availableTables->num_rows > 0) {
            while ($row = $availableTables->fetch_assoc()) {
                echo "<option value='" . $row['table_no'] . "'>Table " . $row['table_no'] . "</option>";
            }
        } else {
            echo "<option value=''>No tables available</option>";
        }
        ?>
    </select>

    Time: <input type="time" name="time" required>
    <button type="submit" name="book_table">Book Table</button>
</form>

<h2>Available Tables</h2>
<ul>
    <?php
    $availableTables = $conn->query("SELECT table_no FROM tables WHERE status = 'Available'");
    if ($availableTables->num_rows > 0) {
        while ($row = $availableTables->fetch_assoc()) {
            echo "<li>Table " . $row['table_no'] . "</li>";
        }
    } else {
        echo "<li>No tables available.</li>";
    }
    ?>
</ul>
