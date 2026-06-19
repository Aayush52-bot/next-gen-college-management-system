<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];

// Fetch user application details, including selected class and academic year
$query = "SELECT a.*, c.course_name, c.fees 
          FROM admissions a 
          JOIN courses c ON a.course_id = c.course_id 
          WHERE a.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

// If no application is found, redirect to the admission form
if (!$application) {
    header("Location: admission.php?course_id=1"); // Change course_id=1 to your default course
    exit();
}

// Fetch the user's full name if not in session
if (!isset($_SESSION['full_name'])) {
    $userQuery = "SELECT full_name FROM users_register WHERE user_id = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();
    $_SESSION['full_name'] = $user['full_name'] ?? $_SESSION['username']; // Fallback to username if full_name not found
    $userStmt->close();
}

// Fetch payment details
$course_id = $application['course_id'];
$category = $application['category'];
$total_fees = $application['fees'];

// Apply discount if category is not General
$discount_amount = ($category !== 'General') ? 20000 : 0;
$final_fees = $total_fees - $discount_amount;

// Check payment history for completed payments
$payment_query = "SELECT SUM(paid_amount) as total_paid, remaining_amount, payment_method 
                  FROM fee_payments 
                  WHERE user_id = ? AND course_id = ? AND payment_status = 'Completed'";
$stmt = $conn->prepare($payment_query);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment = $payment_result->fetch_assoc();

$remaining_fees = $final_fees;
$total_paid = 0;
$previous_payment_method = null;

if ($payment && $payment['total_paid'] !== null) {
    $total_paid = $payment['total_paid'];
    $remaining_fees = $payment['remaining_amount'];
    $previous_payment_method = $payment['payment_method'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Dashboard - NextGen College</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f6f5;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .header {
            background-color: #2c3e50; /* Theme color */
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px; /* Space between hamburger and college name */
        }

        .header .logo-text {
            font-size: 1.5em;
            font-weight: bold;
        }

        .hamburger {
            font-size: 30px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            display: block;
        }

        .sidebar {
            width: 250px;
            background-color: #e0e0e0;
            position: fixed;
            height: calc(100vh - 60px);
            top: 60px;
            left: 0;
            transition: transform 0.3s ease-in-out;
            padding: 20px;
            overflow-y: auto;
            z-index: 999; /* Ensure sidebar is below header but above main content */
        }

        .sidebar.hidden {
            transform: translateX(-100%);
            display: none; /* Ensure it's hidden initially */
        }

        .sidebar.visible {
            transform: translateX(0);
            display: block; /* Ensure it's visible when toggled */
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-info h3 {
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .user-info p {
            font-size: 0.9em;
            color: #555;
        }

        .info-item {
            font-size: 0.9em;
            margin: 5px 0;
            text-align: center;
        }

        .nav-item {
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            display: block;
            font-size: 1em;
        }

        .nav-item.active {
            background-color: #2c3e50; /* Theme color */
            color: white;
        }

        .nav-item:hover {
            background-color: #3498db; /* Accent color */
            color: white;
        }

        .main-content {
            margin-left: 0;
            padding: 80px 20px 20px;
            width: 100%;
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
            background-color: #f5f6f5;
        }

        .main-content.sidebar-open {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .welcome-message {
            font-size: 1.1em;
            margin: 10px 0;
            color: #666;
        }

        .applications-table-container {
            max-height: 300px; /* Fixed height with scrollbar */
            overflow-y: auto;
        }

        .applications-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            display: table; /* Ensure table layout */
        }

        .applications-table th,
        .applications-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .applications-table th {
            background-color: #2c3e50; /* Theme color */
            color: #fff; /* White text for visibility */
            font-weight: normal;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .applications-table td button,
        .applications-table td a {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px 0;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-block;
        }

        .applications-table td .view-btn {
            background-color: #3498db; /* Accent color */
            color: white;
        }

        .applications-table td .pay-btn {
            background-color: #2ecc71; /* Green for pay */
            color: white;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            display: inline-block;
            margin: 2px 0;
            font-size: 0.9em;
        }

        .status.pending {
            background-color: #f1c40f; /* Yellow for pending */
        }

        .status.approved {
            background-color: #2ecc71; /* Green for approved */
        }

        .status.rejected {
            background-color: #e74c3c; /* Red for rejected */
        }

        .fees-paid {
            color: #2ecc71; /* Green for paid */
            font-weight: bold;
            margin: 5px 0;
        }

        .selected-institute {
            color: #e74c3c; /* Red for emphasis */
            font-weight: bold;
            margin: 20px 0 10px;
            font-size: 1em;
        }

        .make-application {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .institute-btn {
            padding: 5px 10px;
            background-color: #e0e0e0;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .institute-btn:hover {
            background-color: #3498db; /* Accent color */
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.visible {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .main-content.sidebar-open {
                margin-left: 0;
                width: 100%;
            }

            .applications-table-container {
                overflow-x: auto; /* Horizontal scroll on small screens */
            }

            .applications-table {
                min-width: 800px; /* Ensure table width for readability */
            }
        }

        /* Wave Animation */
        @keyframes wave {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(-50%) scale(1.1); }
            100% { transform: translateX(0); }
        }

        .wave-in {
            animation: wave 0.5s ease-in-out forwards;
        }

        .user-info-dropdown {
            position: relative;
            cursor: pointer;
        }

        .user-name {
            font-weight: 500;
            color: #fff;
            padding: 5px 10px;
        }

        .user-name:hover {
            background-color: #34495e; /* Darker shade of theme color for hover */
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 1001;
            min-width: 150px;
        }

        .user-info-dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 8px 15px;
            color: #333;
            text-decoration: none;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #3498db; /* Accent color */
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <button class="hamburger">☰</button>
                <span class="logo-text">NextGen College</span>
            </div>
            <div class="user-info-dropdown">
                <span class="user-name"><?php echo htmlspecialchars(strtoupper($_SESSION['full_name'])); ?> ▼</span>
                <div class="dropdown-menu">
                    <a href="change_password.php" class="dropdown-item">Change Password</a>
                    <a href="logout.php" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>

        <div class="sidebar hidden">
            <div class="user-info">
                <div class="profile-pic">👤</div>
                <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <p>Welcome back to your admission portal!</p>
            </div>
            <div class="info-item"><strong>Student ID:</strong> STU202500<?php echo htmlspecialchars($user_id); ?></div>
            <div class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($application['email'] ?? 'Not Provided'); ?></div>
            <div class="info-item"><strong>Program:</strong> <?php echo htmlspecialchars($application['course_name']); ?></div>
            <div class="info-item"><strong>Application Date:</strong> <?php echo htmlspecialchars($application['applied_at'] ?? 'N/A'); ?></div>
            <a href="Student_Portal.php" class="nav-item active">Applications</a>
            <a href="courses.php" class="nav-item">Courses</a>
            <a href="Foxes.html" class="nav-item">Home</a>
        </div>

        <div class="main-content">
            <div class="card">
                <h2>Dashboard</h2>
                <p class="welcome-message">Welcome to Student Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                
                <h3>My Applications</h3>
                <?php if ($application): ?>
                    <div class="applications-table-container">
                        <table class="applications-table">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Application ID</th>
                                    <th>Class</th>
                                    <th>Course</th>
                                    <th>Institute</th>
                                    <th>View/Update Application</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><?php echo htmlspecialchars($application['admission_id']); ?></td>
                                    <td><?php echo htmlspecialchars($application['selected_class']); ?> (A.Y.: <?php echo htmlspecialchars($application['academic_year']); ?>)</td>
                                    <td><?php echo htmlspecialchars($application['course_name']); ?></td>
                                    <td>NextGen College</td>
                                    <td>
                                        <?php if ($application['status'] == 'Pending'): ?>
                                            <a href="updateapp.php?id=<?php echo htmlspecialchars($application['admission_id']); ?>" class="view-btn">Update Application</a>
                                        <?php else: ?>
                                            <a href="viewapp.php?id=<?php echo htmlspecialchars($application['admission_id']); ?>" class="view-btn">View Application</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="status <?php echo strtolower($application['status']); ?>">
                                            <?php echo htmlspecialchars($application['status']); ?>
                                        </div>
                                        <?php if ($application['status'] != 'Pending'): ?>
                                            <?php if ($remaining_fees > 0): ?>
                                                <a href="fee_payment.php?id=<?php echo htmlspecialchars($application['admission_id']); ?>" class="pay-btn">Pay Fees</a>
                                            <?php else: ?>
                                                <div class="fees-paid">Fees Paid</div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="selected-institute">SELECTED UNIVERSITY: SPPU UNIVERSITY</p>
                <?php else: ?>
                    <p class="make-application">No application found.</p>
                    <a href="courses.php" class="institute-btn">Click Here to Make Application</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('visible');
            mainContent.classList.toggle('sidebar-open');
            if (sidebar.classList.contains('visible')) {
                sidebar.classList.add('wave-in');
                setTimeout(() => sidebar.classList.remove('wave-in'), 500);
            }
        });
    </script>
</body>
</html>