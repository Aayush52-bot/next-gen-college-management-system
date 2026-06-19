<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "college";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the inquiries table if it doesn't exist
$sql_create_table = "
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

if ($conn->query($sql_create_table) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Handle form submission
$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $message = $conn->real_escape_string($_POST["message"]);

    $sql = "INSERT INTO inquiries (name, email, message) VALUES ('$name', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {
        $success = "Thank you for contacting us! We will get back to you soon.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact NextGen College for inquiries. Find our address, phone numbers, email, website, and registration details.">
    <meta name="keywords" content="NextGen College, contact, Pune, education, inquiries">
    <meta name="author" content="NextGen College">
    <title>Contact Us - NextGen College</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <link rel="stylesheet" href="./asset/css/contact.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">NextGen College</div>
        <ul class="nav-links">
            <li><a href="Foxes.html">Home</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="Student_Portal.php">Student Portal</a></li>
        </ul>
    </nav>

    <!-- Contact Section -->
    <section class="contact">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-container">
            <!-- Contact Info - Top Section -->
            <div class="contact-details">
                <div class="contact-info-grid">
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Address</h4>
                            <p>Plot No. 42, Innovator Park, Sector 21, Cyber City, Pune – 411057, Maharashtra, India</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Contact Numbers</h4>
                            <p>+91 98230 45678</p>
                            <p>020 6678 8899</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Email</h4>
                            <p><a href="mailto:info@nextgencollege.edu.in">info@nextgencollege.edu.in</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="contact-info-content">
                            <h4>Website</h4>
                            <p><a href="http://www.nextgencollege.edu.in/" target="_blank" rel="noopener noreferrer">www.nextgencollege.edu.in</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="codes">
                    <p>NGU Reg. No. NGU/MU/ESC:042(2020) | AISHE: C-98765 | Jr Col. Code: 22-10-008</p>
                </div>
            </div>

            <!-- Contact Form - Bottom Section -->
            <div class="contact-form-container">
                <div class="contact-form">
                    <h3>Get in Touch</h3>
                    <?php if ($success): ?>
                        <div class="form-message success"><?php echo $success; ?></div>
                    <?php elseif ($error): ?>
                        <div class="form-message error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Your Email</label>
                                <input type="email" id="email" name="email" placeholder="Your Mail Id" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
                        </div>
                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>© 2025 NextGen College. All rights reserved.</p>
    </footer>

    <script>
        // Intersection Observer for Animations
        document.addEventListener('DOMContentLoaded', () => {
            const elements = document.querySelectorAll('.contact-details, .contact-form-container');
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            elements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>

    <?php
    // Close connection
    $conn->close();
    ?>
</body>
</html>