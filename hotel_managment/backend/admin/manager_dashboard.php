<?php
session_start();


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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $phn = $_POST['phn']; 
    $doj = date("Y-m-d"); 
    $role = $_POST['role']; 

    $stmt = $conn->prepare("INSERT INTO employee (name, phn, DOJ, employee_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phn, $doj, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Employee added successfully!'); window.location='manager_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding employee: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

if (isset($_GET['delete_employee'])) {
    $emp_id = $_GET['delete_employee'];

    $stmt = $conn->prepare("DELETE FROM employee WHERE id = ?");
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manager_dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_table_status'])) {
    $table_no = $_POST['table_no'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tables SET status = ? WHERE table_no = ?");
    $stmt->bind_param("si", $status, $table_no);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_room_status'])) {
    $room_no = $_POST['room_no'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE room_no = ?");
    $stmt->bind_param("si", $status, $room_no);
    $stmt->execute();
    $stmt->close();
}


$employees = $conn->query("SELECT * FROM employee");

$attendance = $conn->query("SELECT e.name, e.employee_type AS role, a.date, a.status FROM attendance a JOIN employee e ON a.emp_id = e.id");


$tables = $conn->query("SELECT * FROM tables");


$rooms = $conn->query("SELECT * FROM rooms");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
</head>
<body>

<h1>Welcome, Manager</h1>

<h2>Manage Employees</h2>
<table border="1">
    <tr>
        <th>Employee ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Date of Joining</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $employees->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['phn'] ?></td>
            <td><?= $row['DOJ'] ?></td>
            <td><?= $row['employee_type'] ?></td>
            <td><a href="?delete_employee=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Remove</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Add Employee</h2>
<form method="post">
    <input type="text" name="name" placeholder="Employee Name" required>
    <input type="text" name="phn" placeholder="Phone Number" required>
    <select name="role">
        <option value="Cook">Cook</option>
        <option value="Waiter">Waiter</option>
    </select>
    <button type="submit" name="add_employee">Add Employee</button>
</form>

<h2>Attendance Records</h2>
<table border="1">
    <tr>
        <th>Employee Name</th>
        <th>Role</th>
        <th>Date</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $attendance->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Manage Tables</h2>
<table border="1">
    <tr>
        <th>Table No</th>
        <th>Status</th>
        <th>Update</th>
    </tr>
    <?php while ($row = $tables->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['table_no'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="table_no" value="<?= $row['table_no'] ?>">
                    <select name="status">
                        <option value="Available" <?= $row['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Booked" <?= $row['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                    </select>
                    <button type="submit" name="update_table_status">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Manage Rooms</h2>
<table border="1">
    <tr>
        <th>Room No</th>
        <th>Status</th>
        <th>Update</th>
    </tr>
    <?php while ($row = $rooms->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['room_no'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="room_no" value="<?= $row['room_no'] ?>">
                    <select name="status">
                        <option value="Available" <?= $row['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Booked" <?= $row['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                    </select>
                    <button type="submit" name="update_room_status">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<p><a href="mark_attendance.php">Mark Attendance</a></p>
<p><a href="logout.php">Logout</a></p>

</body>
</html>