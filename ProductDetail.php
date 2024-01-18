<?php
session_start();
include 'db.php'; // Include the database connection

// Check if a product ID is provided
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $product_id = intval($conn->real_escape_string($_GET['product_id'])); // Sanitize the input to prevent SQL Injection

    // Fetch product details from the database
    $query = "SELECT * FROM Products WHERE product_id = $product_id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }
} else {
    die("No product specified.");
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - MyStore</title>
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

    <div class="container">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <!-- Display the image from the image column in the database -->
                <?php if (!empty($product['image'])) : ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-fluid">
                <?php else : ?>
                    <p>No image available.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2>Price: $<?php echo htmlspecialchars($product['price']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <?php if ($isLoggedIn) : ?>
                    <!-- Add to Cart Button -->
                    <form action="AddToCart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="submit" class="btn btn-primary" value="Add to Cart">
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>