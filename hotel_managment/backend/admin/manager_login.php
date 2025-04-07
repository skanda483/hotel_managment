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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phn = $_POST['phn']; 

   
    $stmt = $conn->prepare("SELECT * FROM manager WHERE name = ? AND phn = ?");
    $stmt->bind_param("ss", $name, $phn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['manager'] = $name; 
        header("Location: manager_dashboard.php");
        exit();
    } else {
        $error = "Invalid name or phone number!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login</title>
</head>
<body>

<h2>Manager Login</h2>

<?php if (isset($error)) : ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <input type="text" name="name" placeholder="Manager Name" required>
    <input type="text" name="phn" placeholder="Phone Number" required>
    <button type="submit">Login</button>
</form>

</body>
</html>
