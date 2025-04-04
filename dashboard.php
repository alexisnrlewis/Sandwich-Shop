<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "user";
$password = "pass!";
$dbname = "sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prebuilt sandwich prices
$sandwichPrices = [
    "ham_and_cheese" => 7.99,
    "turkey_club" => 8.99,
    "blt" => 6.99,
    "italian" => 9.49,
    "meatball" => 8.49,
    "chicken_caesar_wrap" => 7.49
];

$taxRate = 0.04; // 4% tax rate

// Calculate the order total
function calculateTotal($sandwichType, $sandwichSize, $toppings) {
    global $sandwichPrices, $taxRate;

    // Base price for the sandwich
    $total = isset($sandwichPrices[$sandwichType]) ? $sandwichPrices[$sandwichType] : 0;

    // Additional charge for custom sandwich size (if applicable)
    if ($sandwichSize == "12") {
        $total += 2.00; // Assuming $2 more for 12-inch sandwiches
    }

    // Add cost for toppings (example: $0.50 per topping)
    $total += count($toppings) * 0.50;

    // Apply tax
    $total += $total * $taxRate;

    return $total;
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data and sanitize it
    $CustUsername = isset($_POST['CustUsername']) ? htmlspecialchars($_POST['CustUsername']) : null;
    $Email = isset($_POST['Email']) ? filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL) : null;
    $Phone = isset($_POST['Phone']) ? preg_replace("/[^0-9]/", "", $_POST['Phone']) : null; // Sanitize phone number
    $Password = isset($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : null;
    $sandwichType = isset($_POST['sandwich_type']) ? $_POST['sandwich_type'] : null;
    $sandwichSize = isset($_POST['sandwich_size']) ? $_POST['sandwich_size'] : null;
    $toppings = isset($_POST['toppings']) ? $_POST['toppings'] : [];

    // Ensure all necessary data is present
    if ($CustUsername && $Email && $Phone && $Password && $sandwichType && $sandwichSize) {
        // Check if user is logged in (assuming user_id is stored in session)
        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in to place an order.";
            exit;
        }

        // Use the session to get the customer ID
        $customerId = $_SESSION['user_id'];

        // Calculate total order amount
        $orderTotal = calculateTotal($sandwichType, $sandwichSize, $toppings);

        // Insert order into Orders table
        $orderQuery = "INSERT INTO Orders (CustomerId, Amount, Status) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("id", $customerId, $orderTotal);

        if ($stmt->execute()) {
            $orderId = $stmt->insert_id; // Get the inserted order ID

            // Insert order items into OrderItems table
            foreach ($toppings as $topping) {
                // Assuming you have a method to get MenuItemId for toppings
                $orderItemQuery = "INSERT INTO OrderItems (OrderId, MenuItemId, Quantity, Price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($orderItemQuery);
                $stmt->bind_param("iiid", $orderId, $topping, 1, 0.50); // Adjust price per topping
                $stmt->execute();
            }

            // Insert payment into Payments table (example: "Cash" payment)
            $paymentQuery = "INSERT INTO Payments (OrderId, PaymentType, AmountPaid) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($paymentQuery);
            $stmt->bind_param("isd", $orderId, $PaymentMethod = "Cash", $orderTotal);

            if ($stmt->execute()) {
                echo "Order placed successfully!";
                
                // Update loyalty points for the user based on order total
                $loyaltyPoints = floor($orderTotal); // 1 point per $1 spent
                $loyaltyQuery = "UPDATE Customers SET LoyaltyPoints = LoyaltyPoints + ? WHERE CustID = ?";
                $stmt = $conn->prepare($loyaltyQuery);
                $stmt->bind_param("ii", $loyaltyPoints, $customerId);
                if ($stmt->execute()) {
                    echo "<br>Loyalty points updated: " . $loyaltyPoints;
                } else {
                    echo "<br>Error updating loyalty points: " . $stmt->error;
                }

            } else {
                echo "Error with payment: " . $stmt->error;
            }

        } else {
            echo "Error with order: " . $stmt->error;
        }

    } else {
        echo "Missing required fields!";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandwich Order Form</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Function to toggle sandwich options based on the selected sandwich type
        function toggleSandwichOptions() {
            const sandwichType = document.querySelector('input[name="sandwich_type"]:checked').value;

            // Show/hide options based on the selected sandwich type
            if (sandwichType === 'prebuilt') {
                document.getElementById('prebuiltOptions').style.display = 'block';
                document.getElementById('customSandwichOptions').style.display = 'none';
                document.getElementById('sandwichSize').style.display = 'block';
                document.getElementById('customSandwich').disabled = true;
            } else if (sandwichType === 'custom') {
                document.getElementById('prebuiltOptions').style.display = 'none';
                document.getElementById('customSandwichOptions').style.display = 'block';
                document.getElementById('sandwichSize').style.display = 'block';
                document.getElementById('customSandwich').disabled = false;
            }
        }

        // Function to calculate and update the order total dynamically
        function updateOrderTotal() {
            const sandwichType = document.querySelector('input[name="sandwich_type"]:checked').value;
            const sandwichSize = document.querySelector('select[name="sandwich_size"]').value;
            const toppings = Array.from(document.querySelectorAll('input[name="toppings[]"]:checked')).map(el => el.value);

            // Use FormData to send POST data to the server
            const formData = new FormData();
            formData.append('sandwich_type', sandwichType);
            formData.append('sandwich_size', sandwichSize);
            toppings.forEach(topping => formData.append('toppings[]', topping));

            // Fetch request to calculate order total on the server
            fetch('calculate_order_total.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('orderTotal').innerText = "Order Total: $" + parseFloat(data).toFixed(2);
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
<h1>Sandwich Order Form</h1>

<form id="orderForm" method="POST" action="process_order.php">
    

 <!-- Order Form -->
 <form id="orderForm">
    <!-- Payment Method -->
    <div>
        <label for="paymentMethod">Payment Method:</label><br>
        <input type="radio" id="cash" name="paymentMethod" value="Cash" required>
        <label for="cash">Cash</label>
        <input type="radio" id="card" name="paymentMethod" value="Card" required>
        <label for="card">Card</label>
    </div>


    <!-- Place Order Button -->
    <button type="submit" id="placeOrderButton">Place Order</button>
</form>

    <!-- Sandwich Type Selection -->
    <h3>Choose a Sandwich Option</h3>
    <input type="radio" name="sandwich_type" value="prebuilt" id="prebuilt" onchange="toggleSandwichOptions()"> Prebuilt Sandwich<br>
    <input type="radio" name="sandwich_type" value="custom" id="custom" onchange="toggleSandwichOptions()"> Build Your Own Sandwich<br>

    <!-- Prebuilt Sandwich Details Section -->
    <div id="prebuiltOptions" style="display: none;">
        <h3>Prebuilt Sandwich Details</h3>
        <label for="prebuilt_sandwich">Select a Prebuilt Sandwich:</label>
        <select name="prebuilt_sandwich" id="prebuilt_sandwich" onchange="updateOrderTotal()" required>
            <option value="">-- Select a Sandwich --</option>
            <option value="ham_and_cheese">Ham and Cheese</option>
            <option value="turkey_club">Turkey Club</option>
            <option value="blt">BLT</option>
            <option value="italian">Italian</option>
            <option value="meatball">Meatball</option>
            <option value="chicken_caesar_wrap">Chicken Caesar Wrap</option>
        </select>
        <label for="sandwich_size">Select Size:</label>
<select name="sandwich_size" id="sandwich_size" onchange="displaySandwichPrice()" required>
    <option value="">-- Select Size --</option>
    <option value="6">6-inch</option>
    <option value="12">12-inch</option>
</select>

<p>Price (with tax): $<span id="sandwichPriceValue">0.00</span></p>
       

    <!-- Build Your Own Sandwich Section (shown when custom is selected) -->
    <div id="customSandwichOptions" style="display: none;">
        <h3>Build Your Own Sandwich</h3>
        <label for="bread_type">Select Bread:</label>
        <select id="bread_type" name="bread_type" onchange="updateOrderTotal()">
            <option value="white">White</option>
            <option value="whole_grain">Whole Grain</option>
            <option value="wrap">Wrap</option>
        </select>
        <br>

<script>
   // Function to toggle visibility of sandwich sections based on the selected sandwich type
function toggleSandwichOptions() {
    const sandwichType = document.querySelector('input[name="sandwich_type"]:checked').value;

    // If 'prebuilt' is selected, show the prebuilt section and hide the build-your-own section
    if (sandwichType === "prebuilt") {
        document.getElementById("prebuiltOptions").style.display = "block";
        document.getElementById("buildYourOwnOptions").style.display = "none";
    }
    // If 'custom' is selected, show the build-your-own section and hide the prebuilt section
    else if (sandwichType === "custom") {
        document.getElementById("prebuiltOptions").style.display = "none";
        document.getElementById("buildYourOwnOptions").style.display = "block";
    }
    else {
        // Hide both sections if none is selected
        document.getElementById("prebuiltOptions").style.display = "none";
        document.getElementById("buildYourOwnOptions").style.display = "none";
    }
}

// Function to update the sandwich price based on the selected sandwich and size
function displaySandwichPrice() {
    const selectedSandwich = document.getElementById("prebuilt_sandwich").value;
    const selectedSize = document.getElementById("sandwich_size").value;

    const sandwichPrices = {
        "ham_and_cheese": { "6": 6.99, "12": 9.99 },
        "turkey_club": { "6": 7.99, "12": 10.99 },
        "blt": { "6": 5.99, "12": 8.99 },
        "italian": { "6": 7.49, "12": 10.49 },
        "meatball": { "6": 6.49, "12": 9.49 },
        "chicken_caesar_wrap": { "6": 6.99, "12": 9.99 }
    };

    // Georgia sales tax rate (4%)
    const taxRate = 0.04;

    if (selectedSandwich && selectedSize) {
        // Get the base price
        const price = sandwichPrices[selectedSandwich][selectedSize];

        // Calculate the tax
        const tax = price * taxRate;

        // Calculate total price including tax
        const totalPrice = price + tax;

        // Display the price with tax
        document.getElementById("sandwichPriceValue").textContent = totalPrice.toFixed(2);
    } else {
        document.getElementById("sandwichPriceValue").textContent = "0.00";
    }

    
}

// Function to toggle sandwich options based on the selected sandwich type
function toggleSandwichOptions() {
    const sandwichType = document.querySelector('input[name="sandwich_type"]:checked').value;

    // Show/hide options based on the selected sandwich type
    if (sandwichType === 'prebuilt') {
        document.getElementById('prebuiltOptions').style.display = 'block';
        document.getElementById('customSandwichOptions').style.display = 'none';
        document.getElementById('sandwichSize').style.display = 'block';
        document.getElementById('customSandwich').disabled = true;
        displaySandwichPrice(); // Call displaySandwichPrice when a prebuilt sandwich is selected
    } else if (sandwichType === 'custom') {
        document.getElementById('prebuiltOptions').style.display = 'none';
        document.getElementById('customSandwichOptions').style.display = 'block';
        document.getElementById('sandwichSize').style.display = 'none';
        document.getElementById('customSandwich').disabled = false;
    }
}

// Add event listener to update price when the size changes
document.querySelector('select[name="sandwich_size"]').addEventListener('change', displaySandwichPrice);

// Add event listener to update price when the sandwich type changes
document.querySelector('select[name="prebuilt_sandwich"]').addEventListener('change', displaySandwichPrice);
</script>

<!-- Sauce Selection (Checkboxes) -->
<h3>Select Sauces</h3>
<label><input type="checkbox" name="sauce" value="mayo" data-price="0.50"> Mayo ($0.50)</label><br>
<label><input type="checkbox" name="sauce" value="mustard" data-price="0.50"> Mustard ($0.50)</label><br>
<label><input type="checkbox" name="sauce" value="italian_dressing" data-price="0.75"> Italian Dressing ($0.75)</label><br>
<label><input type="checkbox" name="sauce" value="caesar" data-price="0.75"> Caesar Dressing ($0.75)</label><br>
<label><input type="checkbox" name="sauce" value="balsamic" data-price="0.75"> Balsamic ($0.75)</label><br>
<label><input type="checkbox" name="sauce" value="hummus" data-price="1.00"> Hummus ($1.00)</label><br>
</div>

<!-- Spice Selection (Checkboxes) -->
<h3>Select Spices</h3>
<label><input type="checkbox" name="spice" value="salt"> Salt</label><br>
<label><input type="checkbox" name="spice" value="pepper"> Pepper</label><br>
<label><input type="checkbox" name="spice" value="oregano"> Oregano</label><br>
<label><input type="checkbox" name="spice" value="basil"> Basil</label><br>
<label><input type="checkbox" name="spice" value="thyme"> Thyme</label><br>
<label><input type="checkbox" name="spice" value="parsley"> Parsley</label><br>
<label><input type="checkbox" name="spice" value="italian seasoning"> Italian Seasoning</label><br>
</div>
<!-- Oils and Vinegar Selection (Checkboxes) -->
<h3>Select Oil and Vinegars</h3>
<label><input type="checkbox" name="spice" value="olive oil"> Olive Oil</label><br>
<label><input type="checkbox" name="spice" value="balsamic vinegar"> Balsamic Vinegar</label><br>
<label><input type="checkbox" name="spice" value="red ine vinegar"> Red Wine Vinegar</label><br>
<label><input type="checkbox" name="spice" value="white wine vinegar"> White Wine Vinegar</label><br>
</div>

        <!-- Toppings Section -->
        <h3>Select Toppings</h3>
        <div class="toppings">
            <div class="topping-category">
                <h4>Meat</h4>
                <label><input type="checkbox" name="topping" value="ham" data-price="1.50"> Ham ($1.50)</label><br>
<label><input type="checkbox" name="topping" value="turkey" data-price="1.50"> Turkey ($1.50)</label><br>
<label><input type="checkbox" name="topping" value="chicken" data-price="1.75"> Chicken ($1.75)</label><br>
<label><input type="checkbox" name="topping" value="bacon" data-price="2.00"> Bacon ($2.00)</label><br>
<label><input type="checkbox" name="topping" value="salami" data-price="1.75"> Salami ($1.75)</label><br>
<label><input type="checkbox" name="topping" value="pepperoni" data-price="1.75"> Pepperoni ($1.75)</label><br>
<label><input type="checkbox" name="topping" value="meatballs" data-price="2.00"> Meatballs ($2.00)</label><br>
            </div>
            <div class="topping-category">
                <h4>Cheese</h4>
                <label><input type="checkbox" name="topping" value="cheddar" data-price="0.75"> Cheddar ($0.75)</label><br>
<label><input type="checkbox" name="topping" value="mozzarella" data-price="0.75"> Mozzarella ($0.75)</label><br>
<label><input type="checkbox" name="topping" value="provolone" data-price="0.75"> Provolone ($0.75)</label><br>
<label><input type="checkbox" name="topping" value="swiss" data-price="0.75"> Swiss ($0.75)</label><br>
<label><input type="checkbox" name="topping" value="american" data-price="0.75"> American ($0.75)</label><br>
            </div>

            <div class="topping-category">
                <h4>Veggies</h4>
                <label><input type="checkbox" name="topping" value="lettuce" data-price="0.50"> Lettuce ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="tomato" data-price="0.50"> Tomato ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="onion" data-price="0.50"> Onion ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="spinach" data-price="0.50"> Spinach ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="bell_peppers" data-price="0.50"> Bell Peppers ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="cucumber" data-price="0.50"> Cucumber ($0.50)</label><br>
<label><input type="checkbox" name="topping" value="avocado" data-price="1.00"> Avocado ($1.00)</label><br>
<label><input type="checkbox" name="topping" value="pickles" data-price="0.50"> Pickles ($0.50)</label><br>

<!-- Display the base price and final price -->
<p>Base Price: $<span id="basePrice">4.99</span></p>
<p>Total Price (with tax): $<span id="finalPrice">4.99</span></p>

<!-- Sandwich Size Selection -->
<label for="sandwichSize">Choose Sandwich Size:</label>
<select id="sandwichSize">
    <option value="6">6-inch - $4.99</option>
    <option value="12">12-inch - $7.99</option>
</select><br><br>

<script>
// Function to update the base price based on the selected sandwich size
function updateBasePrice() {
    // Get the selected sandwich size
    var sandwichSize = document.getElementById('sandwichSize').value;

    // Set the base price based on sandwich size
    var basePrice = (sandwichSize == '12') ? 7.99 : 4.99;  // 12-inch = 7.99, 6-inch = 4.99

    // Update the base price display
    document.getElementById('basePrice').textContent = basePrice.toFixed(2);

    // Recalculate the total price based on the new base price
    calculatePrice(basePrice);
}

// Function to calculate the total price based on the base price, sauces, and toppings
function calculatePrice(basePrice) {
    var totalPrice = basePrice; // Start with the base price

    // Get all selected sauces and toppings
    var selectedSauces = document.querySelectorAll('input[name="sauce"]:checked');
    var selectedToppings = document.querySelectorAll('input[name="topping"]:checked');

    // Add the prices of selected sauces
    selectedSauces.forEach(function(sauce) {
        totalPrice += parseFloat(sauce.getAttribute('data-price'));
    });

    // Add the prices of selected toppings
    selectedToppings.forEach(function(topping) {
        totalPrice += parseFloat(topping.getAttribute('data-price'));
    });

    // Calculate sales tax (assumed 4% for Georgia, change as needed)
    var salesTax = totalPrice * 0.04;

    // Final price = price + tax
    var finalPrice = totalPrice + salesTax;

    // Update the final price display
    document.getElementById('finalPrice').textContent = finalPrice.toFixed(2);
}

// Event listeners for changes in sandwich size, sauce, and topping selections
document.getElementById('sandwichSize').addEventListener('change', function() {
    // Get the selected sandwich size and update the base price accordingly
    updateBasePrice();
});

document.querySelectorAll('input[name="sauce"], input[name="topping"]').forEach(function(input) {
    input.addEventListener('change', function() {
        var basePrice = parseFloat(document.getElementById('basePrice').textContent);  // Get the current base price
        calculatePrice(basePrice);  // Recalculate the total price with the updated base price
    });
});

// Initialize the price calculation when the page loads
window.onload = function() {
    updateBasePrice();  // Initialize base price and total price based on default sandwich size
};
</script>
</script>

</script>

<script>
    // Toggle visibility of sandwich options and toppings/sauces based on the user's selection
    function toggleSandwichOptions() {
        var sandwichType = document.querySelector('input[name="sandwich_type"]:checked').value;

        // Show the prebuilt sandwich options if 'prebuilt' is selected
        if (sandwichType === 'prebuilt') {
            document.getElementById('prebuiltOptions').style.display = 'block';
            document.getElementById('customSandwichOptions').style.display = 'none';
            // Hide the toppings and sauces options for prebuilt sandwiches
            document.getElementById('toppingsOptions').style.display = 'none';
            document.getElementById('sauceOptions').style.display = 'none';
        } else {  // Show the custom sandwich options if 'custom' is selected
            document.getElementById('prebuiltOptions').style.display = 'none';
            document.getElementById('customSandwichOptions').style.display = 'block';
            // Show the toppings and sauces options for custom sandwiches
            document.getElementById('toppingsOptions').style.display = 'block';
            document.getElementById('sauceOptions').style.display = 'block';
        }
    }

     // Initialize the toggle function to set the correct visibility based on the selected radio button on page load
     window.onload = function() {
        toggleSandwichOptions();  // Ensure the correct option is visible when the page loads
    };

    // Add the function to be triggered on the change of sandwich type
    document.querySelectorAll('input[name="sandwich_type"]').forEach(function(radio) {
        radio.addEventListener('change', toggleSandwichOptions);
    });
</script>
