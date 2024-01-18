<?php
include 'db.php'; // Include your database connection

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']); // Sanitize the input

    // Fetch order details (adjust this query as needed for your database structure)
    $query = "SELECT * FROM OrderDetails WHERE order_id = $order_id";
    $result = $conn->query($query);

    if ($result) {
        // Output the details (customize this HTML as needed)
        echo "<h4>Order ID: $order_id</h4>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>Product ID: " . $row['product_id'] . " - Quantity: " . $row['quantity'] . " - Price: $" . $row['price'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "Failed to retrieve order details.";
    }
} else {
    echo "No order ID provided.";
}
