<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Get admin username
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : "Admin";

// Database connection
$conn = new mysqli("localhost", "root", "", "college");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dashboard metrics
// Total Admissions
$total_admissions_query = "SELECT COUNT(*) as total FROM admissions";
$total_admissions_result = $conn->query($total_admissions_query);
$total_admissions = $total_admissions_result->fetch_assoc()['total'];

// Approved Admissions
$approved_admissions_query = "SELECT COUNT(*) as approved FROM admissions WHERE status = 'Approved'";
$approved_admissions_result = $conn->query($approved_admissions_query);
$approved_admissions = $approved_admissions_result->fetch_assoc()['approved'];

// Pending Admissions
$pending_admissions_query = "SELECT COUNT(*) as pending FROM admissions WHERE status = 'Pending'";
$pending_admissions_result = $conn->query($pending_admissions_query);
$pending_admissions = $pending_admissions_result->fetch_assoc()['pending'];

// Total Courses
$total_courses_query = "SELECT COUNT(*) as total FROM courses";
$total_courses_result = $conn->query($total_courses_query);
$total_courses = $total_courses_result->fetch_assoc()['total'];

// Total Users
$total_users_query = "SELECT COUNT(*) as total FROM users_register";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/admin_main.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>NextGen College</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="adminmain.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="manage_courses.php"><i class="fas fa-book"></i> Manage Courses</a></li>
            <li><a href="manage_admissions.php"><i class="fas fa-file-alt"></i> Manage Admissions</a></li>
            <li><a href="manage_payments.php"><i class="fas fa-money-check-alt"></i> Manage Payments</a></li>
            <li><a href="manage_inquiry.php"><i class="fas fa-money-check-alt"></i> Manage Inquiry</a></li>
            <li><a href="adminlogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Navigation -->
        <nav>
            <div class="nav-container">
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <h1>Admin Dashboard</h1>
                <div class="nav-links">
                    <span>Welcome, <?php echo htmlspecialchars($admin_username); ?>!</span>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="container">
            <div class="admin-content">
                <h2>Dashboard Overview</h2>
                <p class="welcome">Monitor and manage NextGen College's operations.</p>

                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3>Total Admissions</h3>
                        <p class="metric"><?php echo $total_admissions; ?></p>
                        <p class="subtext">Applications received</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Approved Admissions</h3>
                        <p class="metric"><?php echo $approved_admissions; ?></p>
                        <p class="subtext">Applications approved</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <h3>Pending Admissions</h3>
                        <p class="metric"><?php echo $pending_admissions; ?></p>
                        <p class="subtext">Applications pending</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>Total Courses</h3>
                        <p class="metric"><?php echo $total_courses; ?></p>
                        <p class="subtext">Courses offered</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Total Users</h3>
                        <p class="metric"><?php echo $total_users; ?></p>
                        <p class="subtext">Registered users</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        hamburger.addEventListener('click', () => {
            if (sidebar.classList.contains('sidebar-open')) {
                // Close the sidebar with wave-out animation
                sidebar.classList.remove('sidebar-open');
                sidebar.classList.add('wave-out');
                setTimeout(() => sidebar.classList.remove('wave-out'), 500);
            } else {
                // Open the sidebar with wave-in animation
                sidebar.classList.add('sidebar-open');
                sidebar.classList.add('wave-in');
                setTimeout(() => sidebar.classList.remove('wave-in'), 500);
            }
            hamburger.classList.toggle('open');
            mainContent.classList.toggle('shifted');
        });
    </script>
</body>
</html>