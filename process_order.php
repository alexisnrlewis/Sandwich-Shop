<?php
session_start();

// Database connection
$servername = "localhost";
$username = "Nora"; // Update if needed
$password = "Liebekigal123!"; // Update if needed
$dbname = "sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if customer is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to place an order.");
}


$customerId = $_SESSION['user_id'];
$selectedSandwich = $_POST['prebuilt_sandwich'] ?? null; // For prebuilt sandwiches
$toppings = $_POST['topping'] ?? []; // Array of toppings
$sauces = $_POST['sauce'] ?? []; // Array of sauces

// Define base prices
$sandwichPrices = [
    "ham_and_cheese" => ["6" => 6.99, "12" => 9.99],
    "turkey_club" => ["6" => 7.99, "12" => 10.99],
    "blt" => ["6" => 5.99, "12" => 8.99],
    "italian" => ["6" => 7.49, "12" => 10.49],
    "meatball" => ["6" => 6.49, "12" => 9.49],
    "chicken_caesar_wrap" => ["6" => 6.99, "12" => 9.99]
];

$toppingPrices = [
    "ham" => 1.50, "turkey" => 1.50, "chicken" => 1.75, "bacon" => 2.00,
    "salami" => 1.75, "pepperoni" => 1.75, "meatballs" => 2.00,
    "cheddar" => 0.75, "mozzarella" => 0.75, "provolone" => 0.75,
    "swiss" => 0.75, "american" => 0.75, "lettuce" => 0.50,
    "tomato" => 0.50, "onion" => 0.50, "spinach" => 0.50,
    "bell_peppers" => 0.50, "cucumber" => 0.50, "avocado" => 1.00,
    "pickles" => 0.50
];

$saucePrices = [
    "mayo" => 0.50, "mustard" => 0.50, "italian_dressing" => 0.75,
    "caesar" => 0.75, "balsamic" => 0.75, "hummus" => 1.00
];

// Calculate the total price
$totalPrice = 0;

// Add sandwich price

// Add toppings prices
foreach ($toppings as $topping) {
    $totalPrice += $toppingPrices[$topping] ?? 0;
}

// Add sauces prices
foreach ($sauces as $sauce) {
    $totalPrice += $saucePrices[$sauce] ?? 0;
}

// Add sales tax (4%)
$totalPrice += $totalPrice * 0.04;

// Store the total amount in the Orders table
$orderQuery = "INSERT INTO Orders (CustomerId, OrderDate, Amount, Status) VALUES (?, NOW(), ?, 'Pending')";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("id", $customerId, $totalPrice);

if ($stmt->execute()) {
    $orderId = $stmt->insert_id; // Get the new order ID

    echo "Order placed successfully! Your Order ID is $orderId. <br>";
    echo "Total Amount: $7.27";

} else {
    echo "Error placing order: " . $stmt->error;
}

// Close the database connection
$stmt->close();
$conn->close();
?>
