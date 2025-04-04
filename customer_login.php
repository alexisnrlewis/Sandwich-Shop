<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "alexislewis";
$password = "ComputerScience";
$dbname = "Sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $custUsername = mysqli_real_escape_string($conn, $_POST['CustUsername']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);

    // Check if the user exists
    $query = "SELECT * FROM customer WHERE CustUsername = '$custUsername'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['Password'])) {
            // Set session variables
            $_SESSION['username'] = $user['CustUsername']; // Use a clearer session name
            $_SESSION['loggedin'] = true;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password
            $login_error = "Invalid password.";
        }
    } else {
        // Username does not exist
        $login_error = "Username does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Customer Login</h2>

        <!-- Login Form -->
        <form method="POST" action="">
            <label for="CustUsername">Username:</label>
            <input type="text" id="CustUsername" name="CustUsername" required>

            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>

            <?php
            // Display error message if login fails
            if (isset($login_error)) {
                echo "<p style='color: red;'>$login_error</p>";
            }
            ?>

            <button type="submit">Login</button>
        </form>

        <!-- If the customer does not have an account, give them an option to register -->
        <p>Don't have an account? <a href="customer_register.php">Register here</a></p>
    </div>
</body>
</html>
