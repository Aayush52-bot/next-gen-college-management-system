<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "college";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);
    
    // Insert into database
    $sql = "INSERT INTO inquiries (name, email, message) VALUES ('$name', '$email', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        // Success - redirect back with success status
        header("Location: Foxes.html#contact?success=1");
        exit();
    } else {
        // Error - redirect back with error status
        header("Location: Foxes.html#contact?error=1");
        exit();
    }
}

$conn->close();
?>