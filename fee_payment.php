<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];

// Fetch user details and course fee
$query = "SELECT a.course_id, a.category, c.fees, a.full_name, a.selected_class, a.academic_year, a.applied_at, c.course_name
          FROM admissions a 
          JOIN courses c ON a.course_id = c.course_id 
          WHERE a.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

if (!$application) {
    die("No admission record found.");
}

$course_id = $application['course_id'];
$category = $application['category'];
$total_fees = $application['fees'];
$full_name = $application['full_name'];
$selected_class = $application['selected_class'];
$academic_year = $application['academic_year'] ?: '2023-2024'; // Default if empty
error_log("Academic Year for user_id $user_id: " . $academic_year);
$applied_at = $application['applied_at'];
$course_name = $application['course_name'];

// Apply discount if category is not General
$discount_amount = ($category !== 'General') ? 20000 : 0;
$final_fees = $total_fees - $discount_amount;

// Check payment history for completed payments (to calculate remaining fees)
$payment_query = "SELECT SUM(paid_amount) as total_paid, payment_method 
                  FROM fee_payments 
                  WHERE user_id = ? AND course_id = ? AND payment_status = 'Completed'";
$stmt = $conn->prepare($payment_query);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment = $payment_result->fetch_assoc();
$stmt->close();

$remaining_fees = $final_fees; // Default to final fees if no payments
$total_paid = 0;
$previous_payment_method = null;

if ($payment && $payment['total_paid'] !== null) {
    $total_paid = $payment['total_paid'];
    $remaining_fees = $final_fees - $total_paid; // Calculate remaining fees dynamically
    $previous_payment_method = $payment['payment_method'];
}

// Fetch payment history for display
$history_query = "SELECT payment_id, payment_method, payment_option, payment_date, academic_year, paid_amount, payment_status 
                  FROM fee_payments 
                  WHERE user_id = ? AND course_id = ? 
                  ORDER BY payment_date DESC";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$history_result = $stmt->get_result();
$payment_history = $history_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['payment_option']) || empty($_POST['payment_option'])) {
        echo "<script>alert('Please select a payment option.'); window.location.href='pay_fees.php';</script>";
        exit();
    }

    if (!isset($_POST['payment_type']) || empty($_POST['payment_type'])) {
        echo "<script>alert('Please select a payment type.'); window.location.href='pay_fees.php';</script>";
        exit();
    }

    $payment_type = $_POST['payment_type']; // "UPI" or "Card"
    $payment_option = $_POST['payment_option']; // "Full," "Half-Yearly," or "Quarterly"
    if (empty($payment_option)) {
        error_log("Payment option is empty for user_id: " . $user_id);
        die("Error: Payment option is empty.");
    }

    // Calculate paid amount based on remaining fees
    $paid_amount = 0;
    switch ($payment_option) {
        case 'Full':
            $paid_amount = $remaining_fees; // Pay the remaining amount
            break;
        case 'Half-Yearly':
            $paid_amount = $remaining_fees * 0.5; // 50% of remaining fees
            break;
        case 'Quarterly':
            $paid_amount = $remaining_fees * 0.25; // 25% of remaining fees
            break;
        default:
            die("Invalid payment option selected.");
    }

    // Ensure paid amount does not exceed remaining fees
    $paid_amount = min($paid_amount, $remaining_fees);
    $remaining_amount = $remaining_fees - $paid_amount;
    $payment_status = 'Pending'; // Always Pending until admin verifies

    // Store payment data in session for both UPI and Card payments
    $_SESSION['payment_data'] = [
        'course_id' => $course_id,
        'category' => $category,
        'total_fees' => $total_fees,
        'discount_amount' => $discount_amount,
        'final_fees' => $final_fees,
        'paid_amount' => $paid_amount,
        'remaining_amount' => $remaining_amount,
        'payment_method' => $payment_type, // Store UPI or Card
        'payment_option' => $payment_option, // Store installment type
        'payment_type' => $payment_type, // Store the payment type (UPI/Card)
        'payment_status' => $payment_status,
        'academic_year' => $academic_year
    ];

    // Redirect based on payment type
    if ($payment_type === 'UPI') {
        header("Location: upi_payment.php");
        exit();
    } else if ($payment_type === 'Card') {
        header("Location: card_payment.php");
        exit();
    } else {
        error_log("Invalid payment type selected: " . $payment_type);
        die("Invalid payment type selected.");
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Details Dashboard - NextGen College</title>
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

        .hamburger {
            font-size: 24px;
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
        }

        .sidebar.hidden {
            transform: translateX(-100%);
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
            background-color: #2c3e50;
            color: white;
        }

        .nav-item:hover {
            background-color: #3498db;
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

        .fee-details-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 4px 4px 0 0;
            font-size: 1.1em;
        }

        .fee-details-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }

        .fee-details-content p {
            margin: 5px 0;
            font-size: 0.9em;
        }

        .fee-details-content p strong {
            display: inline-block;
            width: 150px;
        }

        .fee-payment-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 4px 4px 0 0;
            font-size: 1.1em;
            margin-top: 20px;
        }

        .fee-payment-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }

        .academic-year {
            margin-bottom: 15px;
        }

        .academic-year label {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }

        .academic-year input[type="radio"] {
            margin-right: 5px;
        }

        .payment-options {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .payment-options label {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }

        .payment-options input[type="radio"] {
            margin-right: 5px;
        }

        .payable-fee {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .payable-fee span {
            font-size: 1em;
            font-weight: bold;
        }

        .payable-fee input {
            width: 100px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: right;
        }

        .make-payment-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        .make-payment-btn:hover {
            background-color: #2980b9;
        }

        .make-payment-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        /* Styles for Fee Payment History */
        .fee-history-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 4px 4px 0 0;
            font-size: 1.1em;
            margin-top: 20px;
        }

        .fee-history-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }

        .payment-history-table-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            display: table;
        }

        .payment-history-table th,
        .payment-history-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .payment-history-table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: normal;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .payment-history-table td {
            font-size: 0.9em;
        }

        .receipt-btn {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }

        .receipt-btn:hover {
            background-color: #2980b9;
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

            .payment-history-table-container {
                overflow-x: auto;
            }

            .payment-history-table {
                min-width: 600px; /* Ensure table is scrollable on small screens */
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

        /* Wave-Out Animation */
        @keyframes wave-out {
            0% { transform: translateX(0); }
            50% { transform: translateX(-50%) scale(1.1); }
            100% { transform: translateX(-100%); }
        }

        .wave-out {
            animation: wave-out 0.5s ease-in-out forwards;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <button class="hamburger">☰</button>
                <span>NextGen College</span>
            </div>
            <span><?php echo htmlspecialchars(strtoupper($full_name)); ?></span>
        </div>

        <div class="sidebar hidden">
            <div class="user-info">
                <div class="profile-pic">👤</div>
                <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                <p>Welcome back to your admission portal!</p>
            </div>
            <div class="info-item"><strong>Student ID:</strong> STU202500<?php echo htmlspecialchars($user_id); ?></div>
            <div class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($application['email'] ?? 'Not Provided'); ?></div>
            <div class="info-item"><strong>Program:</strong> <?php echo htmlspecialchars($course_name); ?></div>
            <div class="info-item"><strong>Application Date:</strong> <?php echo htmlspecialchars($applied_at ?? 'N/A'); ?></div>
            <a href="Student_Portal.php" class="nav-item active">Applications</a>
            <a href="courses.php" class="nav-item">Courses</a>
            <a href="Foxes.html" class="nav-item">Home</a>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="fee-details-header">
                    Fee Details of: <?php echo htmlspecialchars(strtoupper($full_name)); ?>        Category: <?php echo htmlspecialchars($category); ?>
                </div>
                <div class="fee-details-content">
                    <p><strong>Enrollment No:</strong> STU202500<?php echo htmlspecialchars($user_id); ?></p>
                    <p><strong>Date of Enrollment:</strong> <?php echo htmlspecialchars($applied_at ?? 'N/A'); ?></p>
                    <p><strong>Class:</strong> <?php echo htmlspecialchars($selected_class); ?> (Academic Year: <?php echo htmlspecialchars($academic_year); ?>)</p>
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($course_name); ?></p>
                    <p><strong>Institute Name:</strong> NextGen College</p>
                </div>

                <div class="fee-payment-header">
                    Fee Payment
                </div>
                <div class="fee-payment-content">
                    <?php if ($remaining_fees <= 0): ?>
                        <p style="color: #2ecc71; font-weight: bold;">All fees have been paid!</p>
                    <?php else: ?>
                        <form method="POST">
                            <div class="academic-year">
                                <label>
                                    <input type="radio" name="academic-year" value="<?php echo htmlspecialchars($academic_year); ?>" checked> Academic Year <?php echo htmlspecialchars($academic_year); ?>
                                </label>
                            </div>
                            <div class="payment-options">
                                <label>
                                    <input type="radio" name="payment_option" value="Full"> Full Pay 
                                </label>
                                <label>
                                    <input type="radio" name="payment_option" value="Half-Yearly"> 50%
                                </label>
                                <label>
                                    <input type="radio" name="payment_option" value="Quarterly"> Quarterly
                                </label>
                            </div>
                            <div class="payment-options">
                                <label>
                                    <input type="radio" name="payment_type" value="UPI" checked> UPI
                                </label>
                                <label>
                                    <input type="radio" name="payment_type" value="Card"> Credit/Debit Card
                                </label>
                            </div>
                            <div class="payable-fee">
                                <span>Payable Amount</span>
                                <input type="text" id="payable-fee" value="<?php echo number_format($remaining_fees, 2); ?>" readonly>
                            </div>
                            <button type="submit" class="make-payment-btn">Make Payment</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="fee-history-header">
                    Fee Payment History
                </div>
                <div class="fee-history-content">
                    <div class="payment-history-table-container">
                        <table class="payment-history-table">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Receipt No.</th>
                                    <th>Payment Method</th>
                                    <th>Payment Option</th>
                                    <th>Payment Date</th>
                                    <th>Academic Year</th>
                                    <th>Amount</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payment_history)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center;">No payment history found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payment_history as $index => $payment): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars('NGC/' . ($payment['academic_year'] ?: 'N/A') . '/' . $payment['payment_id']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_option'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($payment['payment_date']))); ?></td>
                                            <td><?php echo htmlspecialchars($payment['academic_year'] ?: 'N/A'); ?></td>
                                            <td>₹<?php echo number_format($payment['paid_amount'], 2); ?>/-</td>
                                            <td>
                                                <a href="generate_receipt.php?payment_id=<?php echo $payment['payment_id']; ?>" target="_blank">
                                                    <button class="receipt-btn">Receipt</button>
                                                </a>
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
    </div>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        hamburger.addEventListener('click', () => {
            if (sidebar.classList.contains('visible')) {
                sidebar.classList.remove('visible');
                sidebar.classList.add('hidden');
                sidebar.classList.add('wave-out');
                setTimeout(() => sidebar.classList.remove('wave-out'), 500);
            } else {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('visible');
                sidebar.classList.add('wave-in');
                setTimeout(() => sidebar.classList.remove('wave-in'), 500);
            }
            mainContent.classList.toggle('sidebar-open');
        });

        // Dynamically update payable fee based on payment option
        const paymentOptions = document.querySelectorAll('input[name="payment_option"]');
        const payableFeeInput = document.getElementById('payable-fee');
        const remainingFees = <?php echo $remaining_fees; ?>;

        paymentOptions.forEach(option => {
            option.addEventListener('change', () => {
                let payableAmount = remainingFees;
                if (option.value === 'Half-Yearly') {
                    payableAmount = remainingFees * 0.5;
                } else if (option.value === 'Quarterly') {
                    payableAmount = remainingFees * 0.25;
                }
                payableFeeInput.value = payableAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            });
        });

        // Disable form if remaining fees are 0
        <?php if ($remaining_fees <= 0): ?>
            const paymentForm = document.querySelector('form');
            if (paymentForm) {
                paymentForm.querySelectorAll('input, button').forEach(element => {
                    element.disabled = true;
                });
            }
        <?php endif; ?>
    </script>
</body>
</html>