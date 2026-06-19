<?php
session_start();
include 'db.php';

// Check if the user is logged in and has admin role
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}
// Handle payment status update when "Receive" is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    $stmt = $conn->prepare("UPDATE fee_payments SET payment_status = 'Completed' WHERE payment_id = ? AND payment_status = 'Pending'");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to refresh the page and show updated status
    echo "<script>alert('Payment status updated to Completed!'); window.location.href='manage_payments.php';</script>";
    exit();
}

// Fetch all payment records with user and course details
$query = "SELECT fp.*, u.full_name, c.course_name 
          FROM fee_payments fp 
          JOIN users_register u ON fp.user_id = u.user_id 
          JOIN courses c ON fp.course_id = c.course_id 
          ORDER BY fp.payment_date DESC";
$result = $conn->query($query);
$payments = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - NextGen College</title>
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
        }

        .container {
            min-height: 100vh;
        }

        .header {
            background-color: #2c3e50;
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
            gap: 20px;
        }

        .header .logo-text {
            font-size: 1.5em;
            font-weight: bold;
        }

        .header-nav {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 1em;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .nav-link:hover {
            background-color: #34495e;
        }

        .main-content {
            padding: 80px 20px 20px;
            width: 100%;
            background-color: #f5f6f5;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .payments-table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            display: table;
        }

        .payments-table th,
        .payments-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .payments-table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: normal;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .payments-table td button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .receive-btn {
            background-color: #2ecc71;
            color: white;
        }

        .receive-btn:hover {
            background-color: #27ae60;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 0.9em;
        }

        .status.pending {
            background-color: #f1c40f;
        }

        .status.completed {
            background-color: #2ecc71;
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
            }

            .payments-table-container {
                overflow-x: auto;
            }

            .payments-table {
                min-width: 1200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <span class="logo-text">NextGen College</span>
            </div>
            <nav class="header-nav">
                <a href="adminmain.php" class="nav-link">Dashboard</a>
                <a href="adminlogout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <div class="card">
                <h2>Manage Payments</h2>
                <div class="payments-table-container">
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Paid Amount</th>
                                <th>Remaining Amount</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="10" style="text-align: center;">No payments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $index => $payment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                                        <td>₹<?php echo number_format($payment['paid_amount'], 2); ?></td>
                                        <td>₹<?php echo number_format($payment['remaining_amount'], 2); ?></td>
                                        <td>₹<?php echo number_format($payment['final_fees'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                        <td>
                                            <div class="status <?php echo strtolower($payment['payment_status']); ?>">
                                                <?php echo htmlspecialchars($payment['payment_status']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                        <td>
                                            <?php if ($payment['payment_status'] === 'Pending'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['payment_id']); ?>">
                                                    <button type="submit" class="receive-btn">Receive</button>
                                                </form>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>