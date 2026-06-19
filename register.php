<?php
$conn = new mysqli("localhost", "root", "", "college");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];

    // Check existing user with prepared statement
    $stmt = $conn->prepare("SELECT * FROM users_register WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email or Username already exists!";
    } else {
        // Insert with prepared statement
        $stmt = $conn->prepare("INSERT INTO users_register (full_name, email, username, password, phone, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $email, $username, $password, $phone, $gender);
        
        if ($stmt->execute()) {
            $success = "Registration Successful! Redirecting to login...";
            header("Refresh: 2; url=login.php"); // Redirect after 2 seconds
        } else {
            $error = "Error: " . $conn->error;
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - A.M College</title>
    <link rel="stylesheet" href="./asset/css/register.css">
</head>
<body>
    <!-- Background Video -->
    <video autoplay muted loop>
        <source src="./asset/img/course-video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Registration Form Container -->
    <div class="container">
        <div class="form-container">
            <form id="register-form" action="register.php" method="POST">
                <h1>Create Account</h1>

                <?php if (!empty($success)): ?>
                    <div class="success-msg"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="input-field">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                </div>
                <div class="input-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="input-field">
                    <label for="confirm-password">Re-enter Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" required>
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" id="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>

    <script>
        // Password Confirmation Validation
        document.getElementById('register-form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>