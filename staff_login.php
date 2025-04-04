<?php
// Start the session
session_start();

// Include the database connection file
$servername = "localhost";
$username = "alexislewis";
$password = "ComputerScience";
$dbname = "Sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle staff login logic
    if (isset($_POST['login_user'])) {
        $staffUsername = mysqli_real_escape_string($conn, $_POST['StaffUsername']);
        $staffPassword = mysqli_real_escape_string($conn, $_POST['StaffPassword']);

        // Query to check if the staff username exists
        $query = "SELECT * FROM staff WHERE StaffUsername = '$staffUsername'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password (assuming password is stored in plain text for now, but you should hash passwords in production)
            if ($staffPassword === $row['Password']) {
                $_SESSION['staff'] = $staffUsername; // Store staff username in session
                header("Location: staff_inventory.php"); // Redirect to the staff dashboard
                exit();
            } else {
                $login_error = "Invalid username or password.";
            }
        } else {
            $login_error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Staff Login</h2>

        <!-- Login Form -->
        <form method="POST" action="">
            <label for="StaffUsername">Username</label>
            <input type="text" id="StaffUsername" name="StaffUsername" required>

            <label for="StaffPassword">Password</label>
            <input type="password" id="StaffPassword" name="StaffPassword" required>

            <button type="submit" name="login_user">Login</button>
        </form>

        <?php
        // Display error message if any
        if (isset($login_error)) {
            echo "<p style='color: red;'>$login_error</p>";
        }
        ?>
    </div>
</body>
</html>
