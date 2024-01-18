<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit;
}

// Check if product_id is provided
if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
    $product_id = intval($conn->real_escape_string($_POST['product_id']));
    $user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session when logged in

    // Prepare SQL to insert into Cart
    $query = "INSERT INTO Cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)
              ON DUPLICATE KEY UPDATE quantity = quantity + 1"; // This assumes you have a UNIQUE constraint on user_id and product_id

    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Added to Cart'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error adding to cart'); window.location.href='index.php';</script>";
    }
} else {
    // Redirect if product_id is not provided
    header("Location: index.php");
    exit;
}
