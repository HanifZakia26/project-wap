<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}

// Handle user deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['user_id'])) {
    $user_id = intval($conn->real_escape_string($_GET['user_id'])); // Sanitize the input

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Delete referenced data first (Adjust these queries based on your database structure)
        $conn->query("DELETE FROM Cart WHERE user_id = $user_id");
        $conn->query("DELETE FROM OrderHistory WHERE user_id = $user_id");

        // Delete the user
        $conn->query("DELETE FROM Users WHERE user_id = $user_id");

        // Commit transaction
        $conn->commit();
        echo "<script>alert('User deleted successfully');</script>";
    } catch (Exception $e) {
        // An error occurred, roll back the transaction
        $conn->rollback();
        echo "<script>alert('Error deleting user: " . $e->getMessage() . "');</script>";
    }
}
// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    // Sanitize and validate input
    $user_id = intval($conn->real_escape_string($_POST['user_id']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    // Add other fields as necessary

    // Update user details in database
    $update_query = "UPDATE Users SET username = '$username', email = '$email' WHERE user_id = $user_id";

    if ($conn->query($update_query) === TRUE) {
        echo "<script>alert('User updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating user: " . $conn->error . "');</script>";
    }
}

// Fetch users from the database
$users_query = "SELECT user_id, username, email, registration_date FROM Users ORDER BY user_id ASC";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - MyStore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <h2>Manage Users</h2>
        <p>Here you can view, edit, or remove users from the store.</p>
        <a href="AdminDashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

        <?php if ($users_result && $users_result->num_rows > 0) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars(date("F j, Y", strtotime($row['registration_date']))); ?></td>
                            <td>
                                <!-- Trigger/Edit Modal Button -->
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editUserModal<?php echo $row['user_id']; ?>">
                                    Edit
                                </button>
                                <!-- Immediate Delete Link -->
                                <a href="ManageUsers.php?action=delete&user_id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="ManageUsers.php" method="post"> <!-- Adjust action as needed -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Include form fields for editing user information -->
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                            </div>
                                            <!-- Add any additional fields as needed -->
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
            <p>No users found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>