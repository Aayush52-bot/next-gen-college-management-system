<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid']) || !isset($_SESSION['payment_data'])) {
    error_log("Error: User not logged in or payment data missing for Card payment");
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
$payment_method = $payment_data['payment_method'] ?? 'Card'; // Use session value or default to Card
$payment_type = $payment_data['payment_type'] ?? 'Card'; // Card
$payment_option = $payment_data['payment_option'] ?? 'Unknown'; // Full, Half-Yearly, Quarterly
$academic_year = $payment_data['academic_year'] ?? '2023-2024'; // Fallback if not set
$payment_status = $payment_data['payment_status'] ?? 'Pending';

// Log session data for debugging
error_log("Card payment data for user_id $user_id: " . print_r($payment_data, true));

// Handle payment confirmation when "Pay Now" is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'payment_done') {
    // Simulate card payment verification (replace with actual payment gateway API)
    $card_verified = true; // Placeholder: Add Stripe, Razorpay, etc. API check here

    if ($card_verified) {
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
            error_log("Card payment inserted successfully for user_id $user_id");
            $stmt->close();
            // Clear payment data from session
            unset($_SESSION['payment_data']);
            // Return JSON response
            echo json_encode(['status' => 'success', 'message' => 'Payment Initiated! Check your payment status on the student portal later.']);
            exit();
        } else {
            error_log("Card payment insert failed for user_id $user_id: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Payment processing failed']);
            $stmt->close();
            exit();
        }
    } else {
        error_log("Card payment verification failed for user_id $user_id");
        echo json_encode(['status' => 'error', 'message' => 'Card payment verification failed']);
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
    <title>Credit/Debit Card Payment - NextGen College</title>
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

        .payable-amount {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .payable-amount span {
            font-weight: bold;
            color: #2ecc71;
        }

        .card-details {
            text-align: left;
            margin-bottom: 20px;
        }

        .card-details label {
            display: block;
            font-size: 0.9em;
            margin-bottom: 5px;
            color: #666;
        }

        .card-details input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .card-details .card-info {
            display: flex;
            gap: 10px;
        }

        .card-details .card-info input {
            width: 50%;
        }

        .payment-done-btn, .back-btn {
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

        .payment-done-btn:hover, .back-btn:hover {
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
            Credit/Debit Card Payment
        </div>
        <div class="payable-amount">
            Amount to Pay: <span>₹<?php echo number_format($paid_amount, 2); ?></span>
        </div>
        <div class="card-details">
            <label for="card-number">Card Number</label>
            <input type="text" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" required>
            
            <div class="card-info">
                <div>
                    <label for="expiry-date">Expiry Date</label>
                    <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" placeholder="123" maxlength="4" required>
                </div>
            </div>
            
            <label for="card-holder">Card Holder Name</label>
            <input type="text" id="card-holder" placeholder="John Doe" required>
        </div>
        <button class="payment-done-btn" id="payment-done-btn">Pay Now</button>
        <button class="back-btn" onclick="window.location.href='Student_Portal.php'">Back to Dashboard</button>
    </div>

    <script>
        // Payment Done Logic
        document.getElementById('payment-done-btn').addEventListener('click', () => {
            const cardNumber = document.getElementById('card-number').value;
            const expiryDate = document.getElementById('expiry-date').value;
            const cvv = document.getElementById('cvv').value;
            const cardHolder = document.getElementById('card-holder').value;

            if (!cardNumber || !expiryDate || !cvv || !cardHolder) {
                alert('Please fill in all card details.');
                return;
            }

            const btn = document.getElementById('payment-done-btn');
            btn.disabled = true;
            btn.textContent = 'Processing...';

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
                btn.disabled = false;
                btn.textContent = 'Pay Now';
            });
        });

        // Card Number Formatting
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Expiry Date Formatting
        document.getElementById('expiry-date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value.slice(0, 5);
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>