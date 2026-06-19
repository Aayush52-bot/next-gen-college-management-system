<?php
include 'db.php'; // Connect to database

// Define admin credentials
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT); // Hash the password

// Direct SQL Query
$sql = "INSERT INTO admin_users (username, password) VALUES ('$username', '$password')";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Admin user added successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
