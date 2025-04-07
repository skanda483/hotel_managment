<?php
session_start();

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['manager'])) {
    header("Location: manager_login.php");
    exit();
}

$employees = $conn->query("SELECT id, name, employee_type FROM employee");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = date("Y-m-d");

    foreach ($_POST['attendance'] as $emp_id => $status) {
        
        $check = $conn->prepare("SELECT emp_id FROM attendance WHERE emp_id = ? AND date = ?");
        $check->bind_param("is", $emp_id, $date);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE emp_id = ? AND date = ?");
            $stmt->bind_param("sis", $status, $emp_id, $date);
        } else {
            
            $stmt = $conn->prepare("INSERT INTO attendance (emp_id, date, status) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $emp_id, $date, $status);
        }
        
        $stmt->execute();
    }

    echo "<script>alert('Attendance marked successfully!'); window.location='manager_dashboard.php';</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
</head>
<body>

<h1>Mark Attendance</h1>

<form method="post">
    <table border="1">
        <tr>
            <th>Employee Name</th>
            <th>Role</th>
            <th>Attendance</th>
        </tr>
        <?php while ($row = $employees->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['employee_type'] ?></td>
                <td>
                    <select name="attendance[<?= $row['id'] ?>]">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Leave">Leave</option>
                    </select>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <button type="submit">Submit Attendance</button>
</form>

<p><a href="manager_dashboard.php">Back to Dashboard</a></p>

</body>
</html>
