<?php
// courses.php
session_start();
include('db.php'); // Include the database configuration

// Handle search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Build the query based on search and category
$query = "SELECT * FROM courses WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (course_name LIKE '%$search%' OR course_description LIKE '%$search%')";
}

if (!empty($category)) {
    if ($category === 'Junior') {
        $query .= " AND level = 'Junior'";
    } elseif ($category === 'Senior') {
        $query .= " AND level = 'Senior'";
    }
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - NextGen College</title>
    <link rel="stylesheet" href="./asset/css/index.css"> <!-- Your enhanced CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Inline CSS for tabular layout */
        .course-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .courses-table th, .courses-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .courses-table th {
            background: #2c3e50; /* Theme color */
            color: #fff;
            font-weight: 600;
        }
        .courses-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .courses-table tr:hover {
            background: #e8f0fe;
            transition: background 0.3s ease;
        }
        .courses-table td {
            color: #555;
        }
        .apply-btn {
            background: #3498db; /* Theme color */
            color: #fff;
            padding: 0.4rem 0.8rem; /* Reduced padding for a smaller size */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem; /* Smaller font size */
            font-weight: 500; /* Slightly lighter weight */
            transition: background 0.3s ease, transform 0.2s ease; /* Added transform for subtle effect */
        }
        .apply-btn:hover {
            background: #2980b9; /* Darker shade */
            transform: translateY(-2px); /* Subtle lift on hover */
        }
        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: #f4f4f4;
            border-bottom: 1px solid #ddd;
        }
        .search-bar {
            flex: 1;
            margin-right: 1rem;
        }
        .search-bar input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .category-filter select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.5rem;
            color: #2c3e50;
            font-weight: 700;
            position: relative;
        }
        .section-title::after {
            content: '';
            width: 60px;
            height: 4px;
            background: #3498db;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        /* Responsive table */
        @media (max-width: 768px) {
            .courses-table {
                display: block;
                overflow-x: auto;
            }
            .courses-table th, .courses-table td {
                min-width: 150px;
            }
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?> <!-- Include your navigation -->

    <!-- Search and Filter Section -->
    <div class="search-filter">
        <div class="search-bar">
            <form method="GET" action="courses.php">
                <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>
        <div class="category-filter">
            <form method="GET" action="courses.php">
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="Junior" <?php echo ($category === 'Junior') ? 'selected' : ''; ?>>Junior</option>
                    <option value="Senior" <?php echo ($category === 'Senior') ? 'selected' : ''; ?>>Senior</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Courses Section -->
    <section class="course-container">
        <h2 class="section-title">Our Courses</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="courses-table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Level</th>
                        <th>Duration</th>
                        <th>Fees</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['level']); ?></td>
                            <td><?php echo htmlspecialchars($course['duration']); ?></td>
                            <td>₹<?php echo number_format($course['fees'], 2); ?></td>
                            <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                            <td>
                                <a href="admission.php?course_id=<?php echo $course['course_id']; ?>" class="apply-btn">
                                    Apply
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #555;">No courses found matching your criteria.</p>
        <?php endif; ?>
    </section>
    <footer>
        <p>© 2025 NextGen College. All rights reserved.</p>
    </footer>
</body>
</html>