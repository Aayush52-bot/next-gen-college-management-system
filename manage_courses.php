<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Fetch courses from the database
$query = "SELECT * FROM courses ORDER BY course_id";
$result = $conn->query($query);

// Handle course deletion with prepared statement for security
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $deleteStmt->bind_param("i", $course_id);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Course deleted successfully!'); window.location.href='manage_courses.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting course: " . addslashes($conn->error) . "'); window.location.href='manage_courses.php';</script>";
        exit();
    }
    $deleteStmt->close();
}

// Handle course addition
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $level = trim($_POST['level']);
    $duration = trim($_POST['duration']);
    $fees = trim($_POST['fees']);

    // Validate inputs
    if (empty($course_name) || empty($level) || empty($duration) || empty($fees)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($fees) || $fees < 0) {
        $error = "Fees must be a non-negative number.";
    } else {
        // Convert fees to float
        $fees = (float)$fees;

        // Check for duplicate course name
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_name = ? AND course_id != ?");
        $course_id = $_POST['course_id'] ?? 0;
        $stmt->bind_param("si", $course_name, $course_id);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Course name already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_description, level, duration, fees) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $course_name, $description, $level, $duration, $fees);
            
            if ($stmt->execute()) {
                $success = "Course added successfully! 🎉";
            } else {
                $error = "Error adding course: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // Display alert and redirect
    if ($success) {
        echo "<script>alert('$success'); window.location.href='manage_courses.php';</script>";
        exit();
    } elseif ($error) {
        echo "<script>alert('$error'); window.location.href='manage_courses.php';</script>";
        exit();
    }
}

// Handle course editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $level = trim($_POST['level']);
    $duration = trim($_POST['duration']);
    $fees = trim($_POST['fees']);

    // Validate inputs
    if (empty($course_name) || empty($level) || empty($duration) || empty($fees)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($fees) || $fees < 0) {
        $error = "Fees must be a non-negative number.";
    } else {
        // Convert fees to float
        $fees = (float)$fees;

        // Check for duplicate course name
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_name = ? AND course_id != ?");
        $stmt->bind_param("si", $course_name, $course_id);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Course name already exists!";
        } else {
            $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_description = ?, level = ?, duration = ?, fees = ? WHERE course_id = ?");
            $stmt->bind_param("ssssdi", $course_name, $description, $level, $duration, $fees, $course_id);
            
            if ($stmt->execute()) {
                $success = "Course updated successfully! 🚀";
            } else {
                $error = "Error updating course: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // Display alert and redirect
    if ($success) {
        echo "<script>alert('$success'); window.location.href='manage_courses.php';</script>";
        exit();
    } elseif ($error) {
        echo "<script>alert('$error'); window.location.href='manage_courses.php';</script>";
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
    <title>Manage Courses - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/manage_courses.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Course Management</h1>
            <div class="nav-links">
                <a href="adminmain.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="admin-content">
            <h2>Manage Courses</h2>
            <!-- Add Course Button -->
            <button class="btn-add" onclick="openAddModal()">Add Course</button>

            <!-- Success/Error Messages -->
            <div id="success-msg" class="success-msg"></div>
            <div id="error-msg" class="error-msg"></div>

            <!-- Courses Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Level</th>
                            <th>Duration</th>
                            <th>Fees (₹)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                                <td><?php echo htmlspecialchars($course['level']); ?></td>
                                <td><?php echo htmlspecialchars($course['duration']); ?></td>
                                <td>₹<?php echo number_format($course['fees'], 2); ?></td>
                                <td class="action-cell">
                                    <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars($course['course_id']); ?>, '<?php echo htmlspecialchars(addslashes($course['course_name'])); ?>', '<?php echo htmlspecialchars(addslashes($course['course_description'])); ?>', '<?php echo htmlspecialchars(addslashes($course['level'])); ?>', '<?php echo htmlspecialchars(addslashes($course['duration'])); ?>', '<?php echo htmlspecialchars($course['fees']); ?>')">Edit</button>
                                    <a href="manage_courses.php?delete=<?php echo htmlspecialchars($course['course_id']); ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($result->num_rows === 0) { ?>
                            <tr>
                                <td colspan="7">No courses found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Course -->
    <div class="modal" id="addCourseModal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeAddModal()">×</button>
            <h2>Add New Course</h2>
            <form class="add-course-form" action="manage_courses.php" method="POST">
                <div class="input-field">
                    <label for="course_name">Course Name</label>
                    <input type="text" id="course_name" name="course_name" placeholder="Enter course name" required>
                </div>
                <div class="input-field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter description (optional)"></textarea>
                </div>
                <div class="input-field">
                    <label for="level">Level</label>
                    <input type="text" id="level" name="level" placeholder="Enter level (e.g., UG, PG)" required>
                </div>
                <div class="input-field">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" placeholder="Enter duration (e.g., 4 years)" required>
                </div>
                <div class="input-field">
                    <label for="fees">Fees (₹)</label>
                    <input type="number" id="fees" name="fees" placeholder="Enter fees" required min="0" step="0.01">
                </div>
                <button type="submit" name="add_course">Add Course</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Course -->
    <div class="modal" id="editCourseModal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeEditModal()">×</button>
            <h2>Edit Course</h2>
            <form class="edit-course-form" action="manage_courses.php" method="POST">
                <input type="hidden" id="edit_course_id" name="course_id">
                <div class="input-field">
                    <label for="edit_course_name">Course Name</label>
                    <input type="text" id="edit_course_name" name="course_name" required>
                </div>
                <div class="input-field">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description"></textarea>
                </div>
                <div class="input-field">
                    <label for="edit_level">Level</label>
                    <input type="text" id="edit_level" name="level" required>
                </div>
                <div class="input-field">
                    <label for="edit_duration">Duration</label>
                    <input type="text" id="edit_duration" name="duration" required>
                </div>
                <div class="input-field">
                    <label for="edit_fees">Fees (₹)</label>
                    <input type="number" id="edit_fees" name="fees" required min="0" step="0.01">
                </div>
                <button type="submit" name="edit_course">Update Course</button>
            </form>
        </div>
    </div>

    <script>
        // Open Add Modal
        function openAddModal() {
            document.getElementById('addCourseModal').style.display = 'flex';
        }

        // Close Add Modal
        function closeAddModal() {
            document.getElementById('addCourseModal').style.display = 'none';
        }

        // Open Edit Modal
        function openEditModal(course_id, course_name, description, level, duration, fees) {
            document.getElementById('edit_course_id').value = course_id;
            document.getElementById('edit_course_name').value = course_name;
            document.getElementById('edit_description').value = description || ''; // Handle null description
            document.getElementById('edit_level').value = level;
            document.getElementById('edit_duration').value = duration;
            document.getElementById('edit_fees').value = fees;
            document.getElementById('editCourseModal').style.display = 'flex';
        }

        // Close Edit Modal
        function closeEditModal() {
            document.getElementById('editCourseModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addCourseModal');
            const editModal = document.getElementById('editCourseModal');
            if (event.target === addModal) {
                addModal.style.display = 'none';
            }
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
        };

        // Auto-disappear success and error messages
        window.onload = function() {
            const successMsg = document.getElementById('success-msg');
            const errorMsg = document.getElementById('error-msg');

            if ('<?php echo $success; ?>' !== '') {
                successMsg.innerHTML = '<?php echo addslashes($success); ?> <button class="dismiss-btn" onclick="this.parentElement.style.display=\'none\'">OK</button>';
                successMsg.style.display = 'block';
                setTimeout(() => {
                    successMsg.style.display = 'none';
                    window.location.href = 'manage_courses.php'; // Refresh page after message
                }, 3000);
            }

            if ('<?php echo $error; ?>' !== '') {
                errorMsg.innerHTML = '<?php echo addslashes($error); ?> <button class="dismiss-btn" onclick="this.parentElement.style.display=\'none\'">OK</button>';
                errorMsg.style.display = 'block';
                setTimeout(() => {
                    errorMsg.style.display = 'none';
                    window.location.href = 'manage_courses.php'; // Refresh page after message
                }, 3000);
            }
        };
    </script>
</body>
</html>