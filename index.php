<?php
include 'db.php'; // Include your database connection
session_start();

// Dummy check for logged-in status and role
$isLoggedIn = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false;
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';

// Redirect to AdminDashboard.php if logged in as admin
if ($isLoggedIn && $isAdmin) {
    header("Location: AdminDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 100%;
            /* Makes the image fill the container */
            height: 200px;
            /* Fixed height, adjust as needed */
            object-fit: cover;
            /* Ensures the image covers the area nicely */
            object-position: center;
            /* Centers the image within the element */
        }
    </style>

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

    <!-- Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Filters -->
            <div class="col-md-3">
                <h2>Filters</h2>
                <div class="list-group">
                    <?php
                    // Fetch categories from the database
                    $categories = [];
                    $cat_query = "SELECT * FROM Categories"; // Adjust the query as needed
                    $cat_result = $conn->query($cat_query);

                    if ($cat_result && $cat_result->num_rows > 0) {
                        while ($cat_row = $cat_result->fetch_assoc()) {
                            echo '<a href="index.php?category=' . $cat_row['category_id'] . '" class="list-group-item list-group-item-action">' . htmlspecialchars($cat_row['category_name']) . '</a>';
                        }
                    } else {
                        echo '<p>No categories found.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Product Listings -->
            <div class="col-md-9">
                <h2>Product Listings</h2>
                <div class="row">
                    <?php
                    // Initialize an empty array for products
                    $products = [];

                    // Base query for products
                    $product_query = "SELECT * FROM Products";

                    // Check if a category filter is applied
                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $selected_category = intval($conn->real_escape_string($_GET['category'])); // Ensure the category ID is an integer to prevent SQL injection
                        $product_query .= " WHERE category_id = $selected_category"; // Filter by selected category
                    }

                    $product_query .= " LIMIT 10"; // Limit the number of products displayed

                    // Execute the query
                    $product_result = $conn->query($product_query);

                    if ($product_result && $product_result->num_rows > 0) {
                        while ($product_row = $product_result->fetch_assoc()) {
                    ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="images/<?php echo htmlspecialchars($product_row['image']); ?>" class="card-img-top product-image" alt="Product Image">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product_row['product_name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($product_row['description']); ?></p>
                                        <p class="card-text"><small class="text-muted">Price: $<?php echo htmlspecialchars($product_row['price']); ?></small></p>
                                        <a href="ProductDetail.php?product_id=<?php echo $product_row['product_id']; ?>" class="btn btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<p>No products found in this category.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>