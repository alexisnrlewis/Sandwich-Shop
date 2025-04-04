<?php
// Start the session
session_start();

// Include the database connection file
// (make sure you have the correct database credentials here)
$servername = "localhost";
$username = "alexislewis";
$password = "ComputerScience";
$dbname = "Sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted before accessing the POST data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    // Check if the necessary fields are set in POST data
    $CustUsername = isset($_POST['CustUsername']) ? $_POST['CustUsername'] : null;
    $Email = isset($_POST['Email']) ? $_POST['Email'] : null;
    $Phone = isset($_POST['Phone']) ? $_POST['Phone'] : null;
    $Password = isset($_POST['Password']) ? $_POST['Password'] : null;
    $ConfirmPassword = isset($_POST['ConfirmPassword']) ? $_POST['ConfirmPassword'] : null;

    // Ensure all necessary data is present and passwords match
    if ($CustUsername && $Email && $Phone && $Password && $ConfirmPassword) {
        if ($Password === $ConfirmPassword) {
            // Hash the password
            $PasswordHash = password_hash($Password, PASSWORD_DEFAULT);

            // Insert customer data into the Customer table
            $customerQuery = "INSERT INTO Customer (CustUsername, Email, Phone, Password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($customerQuery);
            $stmt->bind_param("ssss", $CustUsername, $Email, $Phone, $PasswordHash);

            if ($stmt->execute()) {
                $registration_success = "Customer registered successfully!";
            } else {
                $registration_error = "Error inserting customer: " . $stmt->error;
            }
        } else {
            $registration_error = "Passwords do not match!";
        }
    } else {
        $registration_error = "Missing required fields!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Customer Registration</h2>

        <!-- Display Success or Error Message -->
        <?php
        if (isset($registration_success)) {
            echo "<p style='color: green;'>$registration_success</p>";
        } else if (isset($registration_error)) {
            echo "<p style='color: red;'>$registration_error</p>";
        }
        ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="CustUsername">Username:</label>
            <input type="text" name="CustUsername" required>

            <label for="Email">Email:</label>
            <input type="email" name="Email" required>

            <label for="Phone">Phone:</label>
            <input type="text" name="Phone" required>

            <label for="Password">Password:</label>
            <input type="password" name="Password" required>

            <label for="ConfirmPassword">Confirm Password:</label>
            <input type="password" name="ConfirmPassword" required>

            <button type="submit" name="register_user">Register</button>
        </form>

        <!-- If the customer already has an account, give them an option to sign in -->
        <p>Already have an account? <a href="customer_login.php">Sign in here</a></p>
    </div>
</body>
</html>
