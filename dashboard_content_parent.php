<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

// Fetch parent details
$parent_query = "SELECT CONCAT(first_name, ' ', last_name) AS name, phone FROM parents WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $parent_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$parent_result = mysqli_stmt_get_result($stmt);
$parent = mysqli_fetch_assoc($parent_result);

// Handle case where no parent data is found
if (!$parent) {
    $parent = ['name' => 'Unknown', 'phone' => 'Unknown'];
}

// Fetch children linked to the parent
$children = mysqli_query($conn, "SELECT students.id AS student_id, CONCAT(students.first_name, ' ', students.last_name) AS name, students.class 
                                 FROM parent_student 
                                 JOIN students ON parent_student.student_id = students.id 
                                 WHERE parent_student.parent_id = {$_SESSION['user_id']}");

// Fetch grades and attendance for each child
$grades = [];
$attendance = [];
while ($child = mysqli_fetch_assoc($children)) {
    $child_id = $child['student_id'];
    $grades[$child_id] = mysqli_query($conn, "SELECT assignments.title AS assignment_title, grades.grade 
                                              FROM grades 
                                              JOIN assignments ON grades.assignment_id = assignments.id 
                                              WHERE grades.student_id = $child_id");
    $attendance[$child_id] = mysqli_query($conn, "SELECT date, status FROM attendance WHERE student_id = $child_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
                <h3><?php echo htmlspecialchars($parent['name']); ?></h3>
                <p>Parent</p>
            </div>
            <div class="nav-items">
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
                <a href="children.php" class="nav-item"><i class="fas fa-users"></i> My Children</a>
                <a href="notifications.php" class="nav-item"><i class="fas fa-bell"></i> Notifications</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="dashboard-header">Parent Dashboard</div>
            <div class="dashboard-cards">
                <?php foreach ($grades as $child_id => $child_grades): ?>
                    <div class="card">
                        <h3>Grades for <?php echo htmlspecialchars($children[$child_id]['name']); ?></h3>
                        <div class="list">
                            <ul>
                                <?php while ($grade = mysqli_fetch_assoc($child_grades)): ?>
                                    <li><?php echo htmlspecialchars($grade['assignment_title'] . ': ' . $grade['grade']); ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($attendance as $child_id => $child_attendance): ?>
                    <div class="card">
                        <h3>Attendance for <?php echo htmlspecialchars($children[$child_id]['name']); ?></h3>
                        <div class="list">
                            <ul>
                                <?php while ($record = mysqli_fetch_assoc($child_attendance)): ?>
                                    <li><?php echo htmlspecialchars($record['date'] . ' - ' . $record['status']); ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="children.php" class="btn">View My Children</a>
                        <a href="notifications.php" class="btn">View Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
