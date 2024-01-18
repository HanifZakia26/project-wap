<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}

// Check if a product_id is provided
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $product_id = intval($conn->real_escape_string($_GET['product_id'])); // Sanitize the input



    // Delete references to the product in the Cart table
    $delete_references_query = "DELETE FROM Cart WHERE product_id = $product_id";
    $conn->query($delete_references_query);

    // Check if the product is referenced in any orders
    $check_order_details_query = "SELECT COUNT(*) as cnt FROM OrderDetails WHERE product_id = $product_id";
    $result = $conn->query($check_order_details_query);
    $row = $result->fetch_assoc();

    if ($row['cnt'] > 0) {
        // The product is referenced in orders, don't delete
        header("Location: ManageProducts.php?error=Product+cannot+be+deleted+as+it+is+referenced+in+orders");
        exit;
    }

    // Then, attempt to delete the product from the Products table
    $delete_product_query = "DELETE FROM Products WHERE product_id = $product_id";
    if ($conn->query($delete_product_query) === TRUE) {
        header("Location: ManageProducts.php?message=Product+deleted+successfully");
    } else {
        header("Location: ManageProducts.php?error=Error+deleting+product");
    }
} else {
    // Redirect back to the ManageProducts.php if no product_id is provided
    header("Location: ManageProducts.php?error=No+product+specified");
}
