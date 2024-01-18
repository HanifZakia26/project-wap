<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session when logged in

// Remove a product from the cart if a delete request is made
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['product_id'])) {
    $product_id = intval($conn->real_escape_string($_GET['product_id']));
    $delete_query = "DELETE FROM Cart WHERE user_id = $user_id AND product_id = $product_id";
    $conn->query($delete_query);
}

// Fetch cart items for the user
$cart_query = "SELECT p.product_id, p.product_name, p.price, c.quantity FROM Cart c
               JOIN Products p ON c.product_id = p.product_id WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_query);


$isLoggedIn = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - MyStore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.php">MyStore</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($isLoggedIn) : ?>
                    <!-- Navbar when logged in -->
                    <li class="nav-item"><a class="nav-link" href="Cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="OrderHistory.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="Profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="Logout.php">Logout</a></li>
                <?php else : ?>
                    <!-- Default Navbar -->
                    <li class="nav-item"><a class="nav-link" href="Login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Your Cart</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cart_result && $cart_result->num_rows > 0) : ?>
                    <?php while ($row = $cart_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price'] * $row['quantity']); ?></td>
                            <td><a href="Cart.php?action=delete&product_id=<?php echo $row['product_id']; ?>" class="btn btn-danger">Remove</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="text-right">
            <a href="Checkout.php" class="btn btn-success">Checkout</a>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>