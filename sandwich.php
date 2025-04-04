<?php
// Start the session at the beginning of the file
session_start();

// Database connection (replace with your actual DB connection details)
$servername = "localhost";
$username = "alexislewis";
$password = "ComputerScience";
$dbname = "Sandwich"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['CustUsername']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);
    
    // Insert user into database (replace with your actual query logic)
    $query = "INSERT INTO customer (CustUsername, Password) VALUES ('$username', '$password')";
    
    if ($conn->query($query) === TRUE) {
        // Redirect to login page after successful registration
        header("Location: login.php"); // Adjust path if necessary
        exit();
    } else {
        $registration_error = "Error: " . $conn->error;
    }
}

// Handle user login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['CustUsername']);
        $password = mysqli_real_escape_string($conn, $_POST['Password']);
        
        // Check user credentials
        $query = "SELECT * FROM customer WHERE CustUsername = '$username' AND Password = '$password'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $_SESSION['user'] = $username; // Store user info in session
            header("Location: sandwich_order_form.php"); // Redirect to order form page
            exit();
        } else {
            $login_error = "Invalid username or password.";
        }
    }
    
    // Handle staff login
    if (isset($_POST['login_staff'])) {
        $staffUsername = mysqli_real_escape_string($conn, $_POST['StaffUsername']);
        $staffPassword = mysqli_real_escape_string($conn, $_POST['StaffPassword']);
        
        // Check staff credentials
        $query = "SELECT * FROM staff WHERE StaffUsername = '$staffUsername' AND Password = '$staffPassword'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $_SESSION['staff'] = $staffUsername; // Store staff info in session
            header("Location: staff_dashboard.php"); // Redirect to staff dashboard
            exit();
        } else {
            $staff_error = "Invalid staff username or password.";
        }
    }
}
?>

<!-- HTML for registration and login forms goes here -->
<!-- Don't forget to close the database connection at the end -->
<?php
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandwich Order Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <!-- Customer Registration Section -->
    <div class="section" id="customer-registration">
        <h2>Customer Registration</h2>
        <form method="POST" action="">
            <label for="CustUsername">Username:</label>
            <input type="text" id="CustUsername" name="CustUsername" required>

            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>

            <label for="ConfirmPassword">Confirm Password:</label>
            <input type="password" id="ConfirmPassword" name="ConfirmPassword" required>

            <button type="submit" name="register_user">Register</button>
        </form>
        <?php if (isset($registration_error)): ?>
            <p style="color: red;"><?php echo $registration_error; ?></p>
        <?php endif; ?>
    </div>

    <!-- User Sign-In Section -->
    <div class="section" id="user-signin">
        <h2>Customer Sign-In</h2>
        <form method="POST" action="">
            <label for="CustUsername">Username:</label>
            <input type="text" id="CustUsername" name="CustUsername" required>

            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>

            <button type="submit" name="login_user">Login</button>
        </form>
        <?php if (isset($login_error)): ?>
            <p style="color: red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
    </div>

    <!-- Staff Sign-In Section -->
    <div class="section" id="staff-signin">
        <h2>Staff Sign-In</h2>
        <form method="POST" action="">
            <label for="StaffUsername">Staff Username:</label>
            <input type="text" id="StaffUsername" name="StaffUsername" required>

            <label for="StaffPassword">Password:</label>
            <input type="password" id="StaffPassword" name="StaffPassword" required>

            <button type="submit" name="login_staff">Login</button>
        </form>
        <?php if (isset($staff_error)): ?>
            <p style="color: red;"><?php echo $staff_error; ?></p>
        <?php endif; ?>
    </div>

    <!-- Sandwich Order Form Section (Visible after User Login) -->
    <?php if (isset($_SESSION['user'])): ?>
    <div class="section" id="sandwich-order-form">
        <h2>Sandwich Order Form</h2>

        <!-- Sandwich Options -->
        <label>Sandwich Type:</label>
        <input type="radio" id="customSandwich" name="sandwich_type" value="custom" checked>
        <label for="customSandwich">Build Your Own Sandwich</label>

        <input type="radio" id="prebuiltSandwich" name="sandwich_type" value="prebuilt">
        <label for="prebuiltSandwich">Select Prebuilt Sandwich</label>

        <!-- Sandwich Details -->
        <h3>Sandwich Details</h3>
        <label for="breadType">Bread Type:</label>
        <select id="breadType" name="bread_type">
            <option value="white">White</option>
            <option value="wheat">Wheat</option>
            <option value="sourdough">Sourdough</option>
            <option value="multigrain">Multigrain</option>
            <option value="wrap">Wrap</option>
        </select>

        <label for="sandwichSize">Sandwich Size:</label>
        <select id="sandwichSize" name="sandwich_size">
            <option value="6">6 inches</option>
            <option value="12">12 inches</option>
        </select>

        <label for="sauce">Sauce:</label>
        <select id="sauce" name="sauce">
            <option value="mayo">Mayo</option>
            <option value="mustard">Mustard</option>
            <option value="italian_dressing">Italian Dressing</option>
            <option value="caesar">Caesar Dressing</option>
            <option value="balsamic">Balsamic</option>
            <option value="hummus">Hummus</option>
        </select>

        <!-- Toppings -->
<h3>Toppings</h3>
<div class="toppings">
    <div class="topping-category">
        <h4>Meat</h4>
        <input type="checkbox" name="toppings[]" value="ham">Ham<br>
        <input type="checkbox" name="toppings[]" value="turkey">Turkey<br>
        <input type="checkbox" name="toppings[]" value="chicken">Chicken<br>
        <input type="checkbox" name="toppings[]" value="bacon">Bacon<br>
        <input type="checkbox" name="toppings[]" value="salami">Salami<br>
        <input type="checkbox" name="toppings[]" value="pepperoni">Pepperoni<br>
        <input type="checkbox" name="toppings[]" value="meatballs">Meatballs<br>
    </div>
    <div class="topping-category">
        <h4>Cheese</h4>
        <input type="checkbox" name="toppings[]" value="cheddar">Cheddar<br>
        <input type="checkbox" name="toppings[]" value="mozzarella">Mozzarella<br>
        <input type="checkbox" name="toppings[]" value="provolone">Provolone<br>
        <input type="checkbox" name="toppings[]" value="swiss">Swiss<br>
        <input type="checkbox" name="toppings[]" value="american">American<br>
    </div>
    <div class="topping-category">
        <h4>Veggies</h4>
        <input type="checkbox" name="toppings[]" value="lettuce">Lettuce<br>
        <input type="checkbox" name="toppings[]" value="tomato">Tomato<br>
        <input type="checkbox" name="toppings[]" value="onion">Onion<br>
        <input type="checkbox" name="toppings[]" value="spinach">Spinach<br>
        <input type="checkbox" name="toppings[]" value="bell_peppers">Bell Peppers<br>
        <input type="checkbox" name="toppings[]" value="cucumber">Cucumber<br>
        <input type="checkbox" name="toppings[]" value="avocado">Avocado<br>
    </div>
</div>

<!-- End of Sandwich Order Form Section -->
<?php endif; ?>

</div> <!-- End of container -->

</body>
</html>

<?php
// Close the database connection
$conn->close();
