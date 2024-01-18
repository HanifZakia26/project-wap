<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Login.php");
    exit;
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
// Fetch user data from the database
$user_id = $_SESSION['user_id']; // Assuming you have user_id stored in session when logged in
$user_query = "SELECT username, email, registration_date FROM Users WHERE user_id = $user_id";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();

// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    // Add other fields as necessary

    // Update user details in database
    $update_query = "UPDATE Users SET username = '$username', email = '$email' WHERE user_id = $user_id";

    if ($conn->query($update_query) === TRUE) {
        echo "<script>alert('Profile updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - MyStore</title>
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
        <h2>Your Profile</h2>
        <p>View or edit your profile information.</p>

        <div class="row">
            <div class="col-md-6">
                <!-- Display user details -->
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                <p><strong>Registration Date:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($user_data['registration_date']))); ?></p>
            </div>
            <div class="col-md-6">
                <!-- Form for updating user details -->
                <form action="Profile.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    <!-- Add additional fields as necessary -->
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>