<?php
session_start();
include 'db.php'; // Ensure your database connection is included

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Fetch users from `users_register` table
$query = "SELECT user_id, full_name, email, username, phone, gender FROM users_register";
$result = $conn->query($query);

if (!$result) {
    die("Query Failed: " . $conn->error); // Debugging: Check query issues
}

// Handle user deletion with prepared statement for security
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM users_register WHERE user_id = ?");
    $deleteStmt->bind_param("i", $user_id);
    if ($deleteStmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    $deleteStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/manage_users.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>User Management</h1>
            <div class="nav-links">
                <a href="adminmain.php">Dashboard</a>
                <a href="adminlogout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="admin-content">
            <h2>Manage Users</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                <td>
                                    <a href="manage_users.php?delete=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($result->num_rows === 0) { ?>
                            <tr>
                                <td colspan="7">No users found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>