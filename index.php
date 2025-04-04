<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Role</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to SBUBBY!</h2>
        <p>Please select your role to proceed:</p>

        <!-- Form to choose between Customer or Staff -->
        <form method="POST" action="">
            <div class="role-selection">
                <button type="submit" name="role" value="customer">Customer</button>
                <button type="submit" name="role" value="staff">Staff</button>
            </div>
        </form>

        <?php
        // Handle the role selection logic
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['role'])) {
                $role = $_POST['role'];

                if ($role == 'customer') {
                    // Redirect to customer registration page
                    header("Location: customer_register.php");
                    exit();
                } elseif ($role == 'staff') {
                    // Redirect to staff login page (Updated)
                    header("Location: staff_login.php");
                    exit();
                }
            }
        }
        ?>
    </div>
</body>
</html>
