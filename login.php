<?php
session_start();

$conn = new mysqli("localhost", "root", "", "college");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Store the previous page the user was on before logging in
if (!isset($_SESSION['previous_page'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // Default to index.php if no referrer
}

// Handle login form submission
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from the database
    $sql = "SELECT * FROM Users_Register WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = $user['user_id'];

            // Redirect back to the page the user was on before login
            header("Location: " . $_SESSION['previous_page']);
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
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
    <title>Login - A.M College</title>
    <link rel="stylesheet" href="./asset/css/log.css">
</head>
<body>
    <video autoplay muted loop>
        <source src="./asset/img/course-video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="container">
        <form id="login-form" action="" method="POST">
            <h1>Welcome Back!</h1>

            <?php if (!empty($success)): ?>
                <div class="success-msg"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="input-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="input-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>