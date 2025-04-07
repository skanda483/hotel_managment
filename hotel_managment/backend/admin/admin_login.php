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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['phn']) && !empty($_POST['password'])) {
        $phn = trim($_POST['phn']);
        $password = trim($_POST['password']);

        $query = "SELECT * FROM admin WHERE phn = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $phn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) { 
                $_SESSION['admin'] = $phn;
                header("Location: admin_dashboard.php"); 
                exit();
            } else {
                $error = "Invalid phone number or password!";
            }
        } else {
            $error = "Invalid phone number or password!";
        }
    } else {
        $error = "Phone number or password missing!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Admin Login</h2>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form action="" method="POST">
    <label>Phone:</label>
    <input type="text" name="phn" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

</body>
</html>
