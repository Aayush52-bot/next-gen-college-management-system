<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $level = $_POST['level'];
    $duration = $_POST['duration'];
    $fees = $_POST['fees'];

    $insertQuery = "INSERT INTO courses (course_name, course_description, level, duration, fees) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssd", $course_name, $course_description, $level, $duration, $fees);

    if ($stmt->execute()) {
        header("Location: manage_courses.php");
        exit();
    } else {
        echo "Error adding course: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="./asset/css/mainad.css">
</head>
<body>

    <div class="container">
        <h2>Add New Course</h2>
        <form method="POST">
            <label>Course Name:</label>
            <input type="text" name="course_name" required>

            <label>Description:</label>
            <textarea name="course_description" required></textarea>

            <label>Level:</label>
            <input type="text" name="level" required>

            <label>Duration:</label>
            <input type="text" name="duration" required>

            <label>Fees (₹):</label>
            <input type="number" name="fees" required>

            <button type="submit">Add Course</button>
        </form>
    </div>

</body>
</html>
