<?php
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Fetch all inquiries
$query = "SELECT * FROM inquiries ORDER BY created_at DESC";
$result = $conn->query($query);
$inquiries = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - NextGen College</title>
    <style>
        /* Same styles reused from your payments page */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f6f5;
            color: #333;
        }

        .container {
            min-height: 100vh;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-text {
            font-size: 1.5em;
            font-weight: bold;
        }

        .header-nav {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 1em;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .nav-link:hover {
            background-color: #34495e;
        }

        .main-content {
            padding: 80px 20px 20px;
            width: 100%;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .inquiries-table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .inquiries-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .inquiries-table th,
        .inquiries-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .inquiries-table th {
            background-color: #2c3e50;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .message-box {
            white-space: pre-wrap;
            max-width: 400px;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .inquiries-table {
                min-width: 1000px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <span class="logo-text">NextGen College</span>
            </div>
            <nav class="header-nav">
                <a href="adminmain.php" class="nav-link">Dashboard</a>
                <a href="adminlogout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <div class="card">
                <h2>Manage Inquiries</h2>
                <div class="inquiries-table-container">
                    <table class="inquiries-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($inquiries)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No inquiries found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($inquiries as $inquiry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inquiry['id']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                        <td class="message-box"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
