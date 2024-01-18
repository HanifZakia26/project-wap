<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session when logged in

// Fetch order history for the user
$history_query = "SELECT order_id, order_date, total_amount FROM OrderHistory WHERE user_id = $user_id ORDER BY order_date DESC";
$history_result = $conn->query($history_query);

$isLoggedIn = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - MyStore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add custom styles here */
        .order-row:hover {
            cursor: pointer;
            background-color: #f8f9fa;
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
    <div class="container mt-4">
        <h2>Your Order History</h2>
        <?php if ($history_result && $history_result->num_rows > 0) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history_result->fetch_assoc()) : ?>
                        <tr class="order-row" data-toggle="modal" data-target="#orderModal" data-order-id="<?php echo $row['order_id']; ?>">
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($row['order_date']))); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have no order history.</p>
        <?php endif; ?>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // JavaScript to handle modal pop-up
        $(document).ready(function() {
            $('.order-row').click(function() {
                var orderId = $(this).data('order-id');
                // Fetch order details from server
                $.ajax({
                    url: 'fetchOrderDetails.php', // You need to create this PHP file
                    method: 'POST',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        // Load the order details into the modal body
                        $('#orderModal .modal-body').html(response);
                    }
                });
            });
        });
    </script>

</body>

</html>