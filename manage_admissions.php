<?php
session_start();
include 'db.php'; // Ensure this file contains database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admission_id = $_POST['admission_id'];
    $new_status = '';

    // Determine the new status based on the button clicked
    if (isset($_POST['approve'])) {
        $new_status = 'Approved';
    } elseif (isset($_POST['reject'])) {
        $new_status = 'Rejected';
    }

    if ($new_status) {
        // Use prepared statement to prevent SQL injection
        $update_query = "UPDATE admissions SET status = ? WHERE admission_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $admission_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Application status updated to $new_status successfully!'); window.location.href='manage_admissions.php';</script>";
        } else {
            echo "<script>alert('Error updating status: " . addslashes($conn->error) . "'); window.location.href='manage_admissions.php';</script>";
        }
        $stmt->close();
    }
}

// Fetch applications
$query = "SELECT * FROM admissions ORDER BY admission_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admissions - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/manage_admissions.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Admission Management</h1>
            <div class="nav-links">
                <a href="adminmain.php">Dashboard</a>
                <a href="manage_courses.php">Manage Courses</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="admin-content">
            <h2>Manage Admissions</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>SSC Marksheet</th>
                            <th>HSC Marksheet</th>
                            <th>Caste Certificate</th>
                            <th>Aadhar Card</th>
                            <th>Degree Certificate</th>
                            <th>Previous Marksheet</th>
                            <th>Transfer Certificate</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['selected_class']); ?></td>
                                <!-- Document Viewing Links -->
                                <td><a href="<?php echo htmlspecialchars($row['ssc_marksheet']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['hsc_marksheet']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['caste_certificate']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['aadhar_card']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['previous_degree_certificate']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['previous_marksheet_path']); ?>" target="_blank">View</a></td>
                                <td><a href="<?php echo htmlspecialchars($row['transfer_certificate']); ?>" target="_blank">View</a></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td class="action-cell">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="admission_id" value="<?php echo htmlspecialchars($row['admission_id']); ?>">
                                        <button type="submit" name="approve" class="btn-approve">Approve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="admission_id" value="<?php echo htmlspecialchars($row['admission_id']); ?>">
                                        <button type="submit" name="reject" class="btn-reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($result->num_rows === 0) { ?>
                            <tr>
                                <td colspan="13">No applications found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>