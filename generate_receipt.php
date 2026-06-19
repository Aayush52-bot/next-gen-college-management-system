<?php
session_start();
include 'db.php';

// Check if the database connection is successful
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];

// Check if payment_id is provided
if (!isset($_GET['payment_id']) || empty($_GET['payment_id'])) {
    die("Error: Payment ID not provided.");
}

$payment_id = intval($_GET['payment_id']);

// Fetch payment details
$query = "SELECT fp.*, c.course_name 
          FROM fee_payments fp 
          JOIN courses c ON fp.course_id = c.course_id 
          WHERE fp.payment_id = ? AND fp.user_id = ? AND fp.payment_status = 'Completed'";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    error_log("Prepare failed for payment query: " . $conn->error);
    die("Error: Failed to prepare payment query. Check error log for details.");
}
$stmt->bind_param("ii", $payment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    die("Error: Payment not found or not completed.");
}

// Check if course_id is valid
if (empty($payment['course_id'])) {
    error_log("Course ID is empty for payment_id: " . $payment_id);
    die("Error: Course ID not found for this payment.");
}

// Fetch student details from admissions and users_register
$query = "SELECT a.full_name, a.selected_class, a.academic_year, u.username 
          FROM admissions a 
          JOIN users_register u ON a.`user_id` = u.`user_id` 
          WHERE a.`user_id` = ? AND a.`course_id` = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    error_log("Prepare failed for student query: " . $conn->error);
    die("Error: Failed to prepare student query. Check error log for details.");
}
$stmt->bind_param("ii", $user_id, $payment['course_id']);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    error_log("No student details found for user_id: " . $user_id . " and course_id: " . $payment['course_id']);
    die("Error: Student details not found.");
}

// Extract payment details
$paid_amount = $payment['paid_amount'];
$total_fees = $payment['final_fees'];
$remaining_amount = $payment['remaining_amount'];
$payment_date = date('d/m/Y', strtotime($payment['payment_date']));
$academic_year = $payment['academic_year'] ?: '2023-2024';
$course_name = $payment['course_name'];
$category = $payment['category'];

// Determine mode of payment based on payment_method
$mode_of_payment = 'Online (Unknown)';
if ($payment['payment_method'] === 'UPI') {
    $mode_of_payment = 'Online (UPI)';
} elseif ($payment['payment_method'] === 'Card') {
    $mode_of_payment = 'Online (Card)';
}

// Generate a unique receipt number
$receipt_no = "UNG/{$academic_year}/{$payment_id}";

// Generate enrollment number (example format)
$enrollment_no = "2023{$user_id}";

// Generate roll number (random for now)
$roll_no = rand(10, 99);

// Function to convert amount to words
function amountToWords($number) {
    $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen'
    ];
    $tens = [
        0 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    ];
    $thousands = [
        0 => '', 1 => 'Thousand', 2 => 'Lakh', 3 => 'Crore'
    ];

    if ($number == 0) {
        return 'Zero';
    }

    $number = round($number, 2);
    $integer_part = floor($number);
    $decimal_part = round(($number - $integer_part) * 100);

    $words = '';
    $level = 0;

    while ($integer_part > 0) {
        $part = $integer_part % 1000;
        $integer_part = floor($integer_part / 1000);

        if ($part > 0) {
            $sub_words = '';
            if ($part >= 100) {
                $sub_words .= $ones[floor($part / 100)] . ' Hundred ';
                $part = $part % 100;
            }
            if ($part >= 20) {
                $sub_words .= $tens[floor($part / 10)] . ' ';
                $part = $part % 10;
            }
            if ($part > 0) {
                $sub_words .= $ones[$part] . ' ';
            }
            $sub_words .= $thousands[$level] . ' ';
            $words = $sub_words . $words;
        }
        $level++;
    }

    $words = trim($words);
    if ($decimal_part > 0) {
        $words .= ' and ';
        if ($decimal_part >= 20) {
            $words .= $tens[floor($decimal_part / 10)] . ' ';
            $decimal_part = $decimal_part % 10;
        }
        if ($decimal_part > 0) {
            $words .= $ones[$decimal_part] . ' ';
        }
        $words .= 'Paisa ';
    }

    $words .= 'Only';
    return $words;
}

// Generate random fee particulars that sum up to paid_amount
$fee_particulars = [
    "Student Insurance Premium" => 0,
    "Laboratory Fees" => 0,
    "Student Activities" => 0,
    "Physical Education Fees" => 0,
    "Test & Tutorial" => 0,
    "University Corpus Fund" => 0,
    "Student Facility" => 0,
    "CBCS Compulsory Credits" => 0,
    "Tuition Fees" => 0,
    "Disaster Management" => 0,
    "Magazine" => 0,
    "Internal & Term End Exam Fees" => 0,
    "Student Welfare Fund" => 0,
    "Student Health Scheme" => 0,
    "Add on Course" => 0,
    "Computerization Fees" => 0,
    "Student Aid Fund" => 0,
    "Common Breakage" => 0,
    "I-Card" => 0,
    "Library Fees" => 0,
    "Pro-rata Contribution for Ashwamegh" => 0,
    "Maintenance" => 0,
    "Admission Fees" => 0,
    "N.S.S" => 0,
    "Sports Fund FIT INDIA" => 0,
    "University Development Fund" => 0,
    "Prize Distribution" => 0,
    "Registration Fees" => 0,
    "Gymkhana Fees" => 0,
    "Eligibility Fees" => 0
];

// Randomly distribute paid_amount across fee particulars
$remaining_amount_to_distribute = $paid_amount;
$keys = array_keys($fee_particulars);
shuffle($keys); // Randomize the order of fee particulars

$num_items = rand(5, min(15, count($keys))); // Use 5 to 15 fee particulars
$selected_keys = array_slice($keys, 0, $num_items);

foreach ($selected_keys as $index => $key) {
    if ($index == $num_items - 1) {
        // Last item gets the remaining amount
        $fee_particulars[$key] = $remaining_amount_to_distribute;
    } else {
        // Randomly allocate a portion of the remaining amount
        $max_allocation = $remaining_amount_to_distribute * 0.3; // Max 30% of remaining per item
        $amount = rand(5, min($max_allocation, $remaining_amount_to_distribute - ($num_items - $index - 1) * 5));
        $fee_particulars[$key] = $amount;
        $remaining_amount_to_distribute -= $amount;
    }
}

// Filter out zero amounts
$fee_particulars = array_filter($fee_particulars, function($value) {
    return $value > 0;
});

// Convert paid_amount to words
$amount_in_words = amountToWords($paid_amount);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #fff;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0;
            font-size: 12pt;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .details, .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details th, .details td, .fee-table th, .fee-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12pt;
        }
        .details th, .fee-table th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .fee-table th:last-child, .fee-table td:last-child {
            text-align: right;
        }
        .total {
            font-weight: bold;
        }
        .amount-words {
            margin: 20px 0;
            font-size: 12pt;
        }
        .cashier {
            margin-top: 40px;
            font-size: 12pt;
        }
        .print-btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14pt;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .print-btn:hover {
            background: #0056b3;
        }
        @media print {
            body {
                margin: 0;
            }
            .container {
                border: none;
                box-shadow: none;
            }
            .print-btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="./asset/img/NextGenLogobg.png" alt="College Logo" style="width: 240px; height: 200px; display: block; margin: 0 auto 10px;">
            <h1>Savitribai Phule Pune University</h1>
            <h1>NextGen College</h1>
            <p>Plot No. 42, Innovator Park,Sector 21, Cyber City, Pune – 411057, Maharashtra, India</p>
            <p>Ph.no. +91 98230 45678 / 020 6678 8899 Email ID: info@nextgencollege.edu.in</p>
            <p>NGU Reg. No. NGU/MU/ESC:042(2020) | AISHE: C-98765 | Jr Col. Code: 22-10-008</p>
            <p>Website: http://www.nextgencollege.edu.in/</p>
        </div>

        <div class="title">
            FEE-RECEIPT (Non Grantable)
        </div>

        <table class="details">
            <tr><th>Receipt No</th><td><?php echo htmlspecialchars($receipt_no); ?></td></tr>
            <tr><th>Dated</th><td><?php echo htmlspecialchars($payment_date); ?></td></tr>
            <tr><th>Enrollment No.</th><td><?php echo htmlspecialchars($enrollment_no); ?></td></tr>
            <tr><th>Roll No.</th><td><?php echo htmlspecialchars($roll_no); ?></td></tr>
            <tr><th>Name</th><td><?php echo htmlspecialchars(strtoupper($student['full_name'])); ?></td></tr>
            <tr><th>Class</th><td><?php echo htmlspecialchars($course_name . ' (' . $academic_year . ')'); ?></td></tr>
            <tr><th>Mode Of Payment</th><td><?php echo htmlspecialchars($mode_of_payment); ?></td></tr>
            <tr><th>Fee Category</th><td><?php echo htmlspecialchars(strtoupper($category)); ?></td></tr>
        </table>

        <table class="fee-table">
            <tr><th>Fee Particulars</th><th>Amount (₹)</th></tr>
            <?php foreach ($fee_particulars as $particular => $amount): ?>
                <tr><td><?php echo htmlspecialchars($particular); ?></td><td><?php echo number_format($amount, 2); ?></td></tr>
            <?php endforeach; ?>
            <tr class="total"><td>Total Amount</td><td><?php echo number_format($paid_amount, 2); ?></td></tr>
        </table>

        <table class="fee-table">
            <tr><th>Total Payable Fees</th><td><?php echo number_format($total_fees, 2); ?></td></tr>
            <tr><th>Total Fees Paid</th><td><?php echo number_format($paid_amount, 2); ?></td></tr>
            <tr><th>Total Outstanding Fees</th><td><?php echo number_format($remaining_amount, 2); ?></td></tr>
        </table>

        <div class="amount-words">
            <p>Amount In Words: <?php echo htmlspecialchars($amount_in_words); ?></p>
            <p><?php echo htmlspecialchars($enrollment_no); ?></p>
        </div>

        <div class="cashier">
            <p>Cashier: (Self)</p>
        </div>

        <div class="print-btn-container">
            <button class="print-btn" onclick="window.print()">Print Receipt</button>
        </div>
    </div>
</body>
</html>