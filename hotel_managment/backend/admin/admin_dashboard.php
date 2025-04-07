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


if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}


$success_msg = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_manager'])) {
    $name = $_POST['name'];
    $phn = $_POST['phn'];
    $doj = $_POST['doj'];

    $stmt = $conn->prepare("INSERT INTO manager (name, phn, DOJ) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phn, $doj);

    if ($stmt->execute()) {
        echo "Manager added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}


if (isset($_GET['delete_manager'])) {
    $manager_id = $_GET['delete_manager'];

    $stmt = $conn->prepare("DELETE FROM manager WHERE id = ?");
    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_dashboard.php");
    exit();
}

$managers = $conn->query("SELECT * FROM manager");


$managers = $conn->query("SELECT * FROM manager");

$total_orders_query = $conn->query("SELECT COUNT(*) AS total FROM bill WHERE status = 'Cleared'");
$total_orders = ($total_orders_query) ? $total_orders_query->fetch_assoc()['total'] : 0;

$total_earnings_query = $conn->query("SELECT SUM(total) AS earnings FROM bill WHERE status = 'Cleared'");
$total_earnings = ($total_earnings_query) ? $total_earnings_query->fetch_assoc()['earnings'] : 0;

$monthly_profit_query = $conn->query("
    SELECT SUM(total) AS profit 
    FROM bill 
    WHERE status = 'Cleared' 
    
");
$monthly_profit = ($monthly_profit_query) ? $monthly_profit_query->fetch_assoc()['profit'] : 0;

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Welcome, Admin</h1>

<?php if ($success_msg): ?>
    <p style="color: green;"><?php echo $success_msg; ?></p>
<?php endif; ?>

<h2>Manage Managers</h2>
<table border="1">
    <tr>
        <th>Manager ID</th>
        <th>Name</th>
        <th>Phone Number</th>
        <th>Date of Joining</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $managers->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['phn'] ?></td>
            <td><?= $row['DOJ'] ?></td>
            <td><a href="?delete_manager=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Add Manager</h2>
<form method="post">
    <input type="text" name="name" placeholder="Enter Manager Name" required>
    <input type="text" name="phn" placeholder="Enter Phone Number" required>
    <input type="date" name="doj" required>
    <button type="submit" name="add_manager">Add Manager</button>
</form>


<h2>Reports</h2>
<table border="1">
    <tr>
        <th>Total Cleared Orders</th>
        <th>Total Cleared Earnings</th>
        <th>Monthly Cleared Profit</th>
    </tr>
    <tr>
        <td><?= $total_orders ?></td>
        <td>$<?= number_format($total_earnings, 2) ?></td>
        <td>$<?= number_format($monthly_profit, 2) ?></td>
    </tr>
</table>

<h2>Real-Time Report Analysis</h2>
<canvas id="reportChart"></canvas>

<script>
    const ctx = document.getElementById('reportChart').getContext('2d');
    const reportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Cleared Orders', ' Earnings', ' Cleared Profit'],
            datasets: [{
                label: 'Business Performance',
                data: [<?= $total_orders ?>, <?= $total_earnings ?>, <?= $monthly_profit ?>],
                backgroundColor: ['blue', 'green', 'orange'],
                borderColor: ['black', 'black', 'black'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<p><a href="logout.php">Logout</a></p>

</body>
</html>