<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $query = "SELECT * FROM courses WHERE course_id = $course_id";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $course = $result->fetch_assoc();
    } else {
        die("Course not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $level = $_POST['level'];
    $duration = $_POST['duration'];
    $fees = $_POST['fees'];

    $updateQuery = "UPDATE courses SET course_name = ?, course_description = ?, level = ?, duration = ?, fees = ? WHERE course_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssdi", $course_name, $course_description, $level, $duration, $fees, $course_id);

    if ($stmt->execute()) {
        header("Location: manage_courses.php");
        exit();
    } else {
        echo "Error updating course: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="./asset/css/mainad.css">
</head>
<body>

    <div class="container">
        <h2>Edit Course</h2>
        <form method="POST">
            <label>Course Name:</label>
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>

            <label>Description:</label>
            <textarea name="course_description" required><?php echo htmlspecialchars($course['course_description']); ?></textarea>

            <label>Level:</label>
            <input type="text" name="level" value="<?php echo htmlspecialchars($course['level']); ?>" required>

            <label>Duration:</label>
            <input type="text" name="duration" value="<?php echo htmlspecialchars($course['duration']); ?>" required>

            <label>Fees (₹):</label>
            <input type="number" name="fees" value="<?php echo htmlspecialchars($course['fees']); ?>" required>

            <button type="submit">Update Course</button>
        </form>
    </div>

</body>
</html>
