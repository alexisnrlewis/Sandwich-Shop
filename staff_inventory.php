<?php
// Start the session
session_start();

// Include the database connection file
$servername = "localhost";
$username = "user";
$password = "pass!";
$dbname = "sandwich";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch inventory details
$sql = "SELECT i.IngName, i.Category, inv.AmountInStock, inv.ReorderThreshold, inv.LastUpdated 
        FROM Inventory inv
        JOIN Ingredients i ON i.IngredientId = inv.IngredientId";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Inventory Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <h1>Inventory Management</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Ingredient</th>
                        <th>Category</th>
                        <th>Amount in Stock</th>
                        <th>Reorder Threshold</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr <?php if ($row['AmountInStock'] < $row['ReorderThreshold']) echo "style='background-color: #ffcccc;'"; ?>>
                            <td><?php echo htmlspecialchars($row['IngName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Category']); ?></td>
                            <td><?php echo htmlspecialchars($row['AmountInStock']); ?></td>
                            <td><?php echo htmlspecialchars($row['ReorderThreshold']); ?></td>
                            <td><?php echo htmlspecialchars($row['LastUpdated']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No inventory data available.</p>
        <?php endif; ?>

    </div>
</body>
</html>
<?php
$conn->close();
?>
