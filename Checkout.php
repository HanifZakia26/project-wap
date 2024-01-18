<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session when logged in

// Start transaction
$conn->begin_transaction();

try {
    // Calculate total amount from the cart
    $cart_query = "SELECT SUM(p.price * c.quantity) AS total FROM Cart c
                   JOIN Products p ON c.product_id = p.product_id WHERE c.user_id = $user_id";
    $cart_result = $conn->query($cart_query);
    $row = $cart_result->fetch_assoc();
    $total_amount = $row['total'];

    // Insert into OrderHistory
    $history_query = "INSERT INTO OrderHistory (user_id, total_amount) VALUES ($user_id, $total_amount)";
    $conn->query($history_query);
    $order_id = $conn->insert_id; // Get the order ID

    // Fetch products from cart and insert into OrderDetails
    $details_query = "SELECT c.product_id, c.quantity, (p.price * c.quantity) AS price FROM Cart c
JOIN Products p ON c.product_id = p.product_id WHERE c.user_id = $user_id";
    $details_result = $conn->query($details_query);

    while ($detail = $details_result->fetch_assoc()) {
        $conn->query("INSERT INTO OrderDetails (order_id, product_id, quantity, price) VALUES ($order_id, " .
            $detail['product_id'] . ", " . $detail['quantity'] . ", " . $detail['price'] . ")");
    }

    // Clear the cart
    $conn->query("DELETE FROM Cart WHERE user_id = $user_id");

    // Commit transaction
    $conn->commit();

    // Success message and redirect
    echo "<script>alert('Checkout Success'); window.location.href='index.php';</script>";
} catch (Exception $e) {
    // An error occurred, roll back the transaction
    $conn->rollback();
    echo "<script>alert('Checkout Failed: " . addslashes($e->getMessage()) . "'); window.location.href='Cart.php';</script>";
}
