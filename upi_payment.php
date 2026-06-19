<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid']) || !isset($_SESSION['payment_data'])) {
    error_log("Error: User not logged in or payment data missing for UPI payment");
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];
$payment_data = $_SESSION['payment_data'];

// Extract payment data from session
$course_id = $payment_data['course_id'];
$category = $payment_data['category'];
$total_fees = $payment_data['total_fees'];
$discount_amount = $payment_data['discount_amount'];
$final_fees = $payment_data['final_fees'];
$paid_amount = $payment_data['paid_amount'];
$remaining_amount = $payment_data['remaining_amount'];
$payment_method = $payment_data['payment_method'] ?? 'UPI'; // Ensure UPI
$payment_type = $payment_data['payment_type'] ?? 'UPI'; // UPI
$payment_option = $payment_data['payment_option'] ?? 'Unknown'; // Full, Half-Yearly, Quarterly
$academic_year = $payment_data['academic_year'] ?? '2023-2024'; // Fallback if not set
$payment_status = $payment_data['payment_status'] ?? 'Pending';

// Log session data for debugging
error_log("UPI payment data for user_id $user_id: " . print_r($payment_data, true));

// Handle payment confirmation when "Payment Done" is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'payment_done') {
    // Simulate UPI payment verification (replace with actual payment gateway API)
    $upi_verified = true; // Placeholder: Add Razorpay, Paytm, etc. API check here

    if ($upi_verified) {
        // Insert payment record into the database
        $stmt = $conn->prepare("INSERT INTO fee_payments (
            user_id, course_id, category, total_fees, discount_amount, final_fees, 
            paid_amount, remaining_amount, payment_method, payment_type, payment_option, 
            academic_year, payment_status, payment_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            error_log("Prepare failed for user_id $user_id: " . $conn->error);
            die(json_encode(['status' => 'error', 'message' => 'Database error']));
        }
        $stmt->bind_param("iisddddssssss", 
            $user_id, $course_id, $category, $total_fees, $discount_amount, $final_fees, 
            $paid_amount, $remaining_amount, $payment_method, $payment_type, $payment_option, 
            $academic_year, $payment_status
        );
        if ($stmt->execute()) {
            error_log("UPI payment inserted successfully for user_id $user_id");
            $stmt->close();
            // Clear payment data from session
            unset($_SESSION['payment_data']);
            // Return JSON response
            echo json_encode(['status' => 'success', 'message' => 'Payment Initiated! Check your payment status on the student portal later.']);
            exit();
        } else {
            error_log("UPI payment insert failed for user_id $user_id: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Payment processing failed']);
            $stmt->close();
            exit();
        }
    } else {
        error_log("UPI payment verification failed for user_id $user_id");
        echo json_encode(['status' => 'error', 'message' => 'UPI payment verification failed']);
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment - NextGen College</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .payment-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .payment-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 4px 4px 0 0;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .qr-code {
            margin: 20px 0;
        }

        .qr-code img {
            width: 200px;
            height: 200px;
        }

        .payable-amount {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .payable-amount span {
            font-weight: bold;
            color: #2ecc71;
        }

        .instructions {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
        }

        .timer {
            font-size: 1.5em;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .retry-message {
            font-size: 1em;
            color: #333;
            margin-bottom: 20px;
            display: none;
        }

        .retry-btn, .payment-done-btn, .back-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            margin: 5px;
            transition: background 0.3s ease;
        }

        .back-btn {
            background-color: #95a5a6;
        }

        .retry-btn:hover, .payment-done-btn:hover, .back-btn:hover {
            background-color: #2980b9;
        }

        .back-btn:hover {
            background-color: #7f8c8d;
        }

        .payment-done-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            UPI Payment
        </div>
        <div class="qr-code">
            <img src="./asset/img/upi2.jpg" alt="UPI QR Code">
        </div>
        <div class="payable-amount">
            Amount to Pay: <span>₹<?php echo number_format($paid_amount, 2); ?></span>
        </div>
        <div class="instructions">
            Scan the QR code using your UPI app to make the payment. Complete the payment within the time limit.
        </div>
        <div class="timer" id="timer">02:00</div>
        <div class="retry-message" id="retry-message">
            Time's up! Do you want to retry?
        </div>
        <button class="retry-btn" id="retry-btn" style="display: none;">Retry</button>
        <button class="payment-done-btn" id="payment-done-btn">Payment Done</button>
        <button class="back-btn" id="back-btn" onclick="window.location.href='Student_Portal.php'">Back to Dashboard</button>
    </div>

    <script>
        let timeLeft = 120; // 2 minutes in seconds
        const timerElement = document.getElementById('timer');
        const retryMessage = document.getElementById('retry-message');
        const retryBtn = document.getElementById('retry-btn');
        const paymentDoneBtn = document.getElementById('payment-done-btn');

        function startTimer() {
            const timer = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timerElement.style.display = 'none';
                    retryMessage.style.display = 'block';
                    retryBtn.style.display = 'inline-block';
                    paymentDoneBtn.style.display = 'none';
                } else {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    timeLeft--;
                }
            }, 1000);
        }

        startTimer();

        retryBtn.addEventListener('click', () => {
            timeLeft = 120;
            timerElement.style.display = 'block';
            retryMessage.style.display = 'none';
            retryBtn.style.display = 'none';
            paymentDoneBtn.style.display = 'inline-block';
            startTimer();
        });

        paymentDoneBtn.addEventListener('click', () => {
            paymentDoneBtn.disabled = true;
            paymentDoneBtn.textContent = 'Processing...';

            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=payment_done'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = 'Student_Portal.php';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                paymentDoneBtn.disabled = false;
                paymentDoneBtn.textContent = 'Payment Done';
            });
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>