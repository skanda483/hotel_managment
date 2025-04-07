<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "management";  

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$available_rooms = $conn->query("SELECT room_no FROM rooms WHERE status = 'Available'");

if (isset($_POST['book_room'])) {
    $room_no = $_POST['room_no'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guest_name = $_POST['guest_name'];
    $phone = $_POST['phone'];

    $user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $user_query->bind_param("s", $guest_name);
    $user_query->execute();
    $user_result = $user_query->get_result();

    if ($user_result->num_rows == 0) {
        $default_email = strtolower(str_replace(" ", "_", $guest_name)) . "@guest.com";
        $insert_user = $conn->prepare("INSERT INTO users (username, phone, email) VALUES (?, ?, ?)");
        $insert_user->bind_param("sss", $guest_name, $phone, $default_email);
        
        if ($insert_user->execute()) {
            $user_id = $insert_user->insert_id;
        } else {
            die("Error inserting new user: " . $conn->error);
        }
    } else {
        $user_row = $user_result->fetch_assoc();
        $user_id = $user_row['id'];
    }

    $update_room = $conn->prepare("UPDATE rooms SET status = 'Booked', check_in = ?, check_out = ?, user_id = ?, phone = ? WHERE room_no = ?");
    $update_room->bind_param("ssiis", $check_in, $check_out, $user_id, $phone, $room_no);

    if ($update_room->execute()) {
        $message = "Room booked successfully!";
    } else {
        $message = "Error booking the room. Please try again.";
    }
}
?>

<h1>Book a Room</h1>
<?php if (isset($message)) { echo "<p><strong>$message</strong></p>"; } ?>

<form method="POST">
    Guest Name: <input type="text" name="guest_name" required><br>
    Phone: <input type="text" name="phone" required><br>
    
    Room Number: 
    <select name="room_no" required>
        <option value="">Select a Room</option>
        <?php while ($row = $available_rooms->fetch_assoc()) { ?>
            <option value="<?= $row['room_no']; ?>"><?= $row['room_no']; ?></option>
        <?php } ?>
    </select><br>

    Check-In Date: <input type="date" name="check_in" required><br>
    Check-Out Date: <input type="date" name="check_out" required><br>
    
    <button type="submit" name="book_room">Book Room</button>
</form>
