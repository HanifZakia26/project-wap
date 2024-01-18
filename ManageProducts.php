<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}

// Handle form submission to update product details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    // Sanitize and validate input
    $product_id = intval($conn->real_escape_string($_POST['product_id']));
    $product_name = $conn->real_escape_string(trim($_POST['product_name']));
    $price = floatval($conn->real_escape_string(trim($_POST['price'])));
    $stock_quantity = intval($conn->real_escape_string(trim($_POST['stock_quantity'])));

    // Update product details in database
    $update_query = "UPDATE Products SET product_name = '$product_name', price = $price, stock_quantity = $stock_quantity WHERE product_id = $product_id";

    if ($conn->query($update_query) === TRUE) {
        echo "<script>alert('Product updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "');</script>";
    }
}

// Fetch products from the database
$products_query = "SELECT * FROM Products ORDER BY product_id ASC";
$products_result = $conn->query($products_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - MyStore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <h2>Manage Products</h2>
        <p>Here you can add, edit, or remove products from the store.</p>
        <a href="AdminDashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <!-- Possibly add a button for adding new products -->

        <?php if ($products_result && $products_result->num_rows > 0) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $products_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                            <td>
                                <!-- Trigger/Edit Modal Button -->
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editProductModal<?php echo $row['product_id']; ?>">
                                    Edit
                                </button>
                                <a href="DeleteProduct.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>

                        <!-- Edit Product Modal -->
                        <div class="modal fade" id="editProductModal<?php echo $row['product_id']; ?>" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="ManageProducts.php" method="post"> <!-- Change action as needed -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                            <div class="form-group">
                                                <label>Product Name</label>
                                                <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($row['price']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Stock Quantity</label>
                                                <input type="number" class="form-control" name="stock_quantity" value="<?php echo htmlspecialchars($row['stock_quantity']); ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Confirm</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>