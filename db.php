<?php
$servername = "localhost";
$username = "root";  // Default XAMPP user
$password = "";  // Default is empty for XAMPP
$database = "college";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
