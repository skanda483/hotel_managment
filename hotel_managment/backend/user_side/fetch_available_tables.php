<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$query = "SELECT table_no FROM kitchen WHERE serve = 'Pending'";
$result = $conn->query($query);
$tables = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>Table " . $row['table_no'] . " is available.</p>";
    }
} else {
    echo "<p>No tables available.</p>";
}
?>
