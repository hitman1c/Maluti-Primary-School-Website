<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch admin details
$admin_query = "SELECT username FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $admin_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$admin_result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($admin_result);

// Handle case where no admin data is found
if (!$admin) {
    $admin = ['username' => 'Unknown'];
}

// Fetch counts for statistics
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"))['count'];
$teacher_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM teachers"))['count'];
$parent_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parents"))['count'];
$admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"))['count'];

// Fetch teachers, students, and classes
$teachers = mysqli_query($conn, "SELECT CONCAT(first_name, ' ', last_name) AS name FROM teachers");
$students = mysqli_query($conn, "SELECT CONCAT(first_name, ' ', last_name) AS name FROM students");
$classes = mysqli_query($conn, "SELECT name FROM classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            padding: 20px;
        }

        .sidebar .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .profile-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .sidebar .nav-items {
            list-style: none;
            padding: 0;
        }

        .sidebar .nav-item {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background 0.3s ease;
        }

        .sidebar .nav-item:hover {
            background-color: #0056b3;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.8em;
            font-weight: bold;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33.333% - 40px);
            min-width: 300px;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
        }

        .list {
            max-height: 200px;
            overflow-y: auto;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
                <h3><?php echo htmlspecialchars($admin['username']); ?></h3>
                <p>Admin</p>
            </div>
            <div class="nav-items">
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_students.php" class="nav-item"><i class="fas fa-users"></i> Manage Students</a>
                <a href="manage_teachers.php" class="nav-item"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
                <a href="manage_classes.php" class="nav-item"><i class="fas fa-school"></i> Manage Classes</a>
                <a href="fee_management.php" class="nav-item"><i class="fas fa-dollar-sign"></i> Fee Management</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                <a href="admin_manage_passwords.php" class="nav-item"><i class="fas fa-key"></i> Manage Passwords</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="dashboard-header">Admin Dashboard</div>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Statistics</h3>
                    <div class="stats-grid" style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-around;">
                        <div style="background: #1cc88a; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                            <h4>Students</h4>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $student_count; ?></p>
                        </div>
                        <div style="background: #36b9cc; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                            <h4>Teachers</h4>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $teacher_count; ?></p>
                        </div>
                        <div style="background: #f6c23e; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                            <h4>Parents</h4>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $parent_count; ?></p>
                        </div>
                        <div style="background: #e74a3b; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                            <h4>Admins</h4>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $admin_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h3>Manage Teachers</h3>
                    <div class="list">
                        <ul>
                            <?php while ($teacher = mysqli_fetch_assoc($teachers)): ?>
                                <li><?php echo htmlspecialchars($teacher['name']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <h3>Manage Students</h3>
                    <div class="list">
                        <ul>
                            <?php while ($student = mysqli_fetch_assoc($students)): ?>
                                <li><?php echo htmlspecialchars($student['name']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <h3>Manage Classes</h3>
                    <div class="list">
                        <ul>
                            <?php while ($class = mysqli_fetch_assoc($classes)): ?>
                                <li><?php echo htmlspecialchars($class['name']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="manage_teachers.php" class="btn">Manage Teachers</a>
                        <a href="manage_students.php" class="btn">Manage Students</a>
                        <a href="manage_classes.php" class="btn">Manage Classes</a>
                        <a href="admin_manage_passwords.php" class="btn">Manage Passwords</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
