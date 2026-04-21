<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

// Fetch parent's children
$children_query = "SELECT students.id, CONCAT(students.first_name, ' ', students.last_name) AS name, students.class 
                   FROM students 
                   JOIN parents ON students.user_id = parents.user_id 
                   WHERE parents.user_id = ?";
$stmt = mysqli_prepare($conn, $children_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$children_result = mysqli_stmt_get_result($stmt);
$children = mysqli_fetch_all($children_result, MYSQLI_ASSOC);
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
        /* ...existing styles... */
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
                <h3>Parent</h3>
                <p>Welcome</p>
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
                <?php foreach ($children as $child): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($child['name']); ?> (Class: <?php echo htmlspecialchars($child['class']); ?>)</h3>
                        <h4>Grades</h4>
                        <ul class="list">
                            <?php
                            $grades_query = "SELECT assignments.title, grades.grade 
                                             FROM grades 
                                             JOIN assignments ON grades.assignment_id = assignments.id 
                                             WHERE grades.student_id = ?";
                            $stmt = mysqli_prepare($conn, $grades_query);
                            mysqli_stmt_bind_param($stmt, "i", $child['id']);
                            mysqli_stmt_execute($stmt);
                            $grades_result = mysqli_stmt_get_result($stmt);
                            while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                                <li><?php echo htmlspecialchars($grade['title'] . ': ' . $grade['grade']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                        <h4>Attendance</h4>
                        <ul class="list">
                            <?php
                            $attendance_query = "SELECT schedule_date, event_type 
                                                 FROM attendance 
                                                 WHERE student_id = ?";
                            $stmt = mysqli_prepare($conn, $attendance_query);
                            mysqli_stmt_bind_param($stmt, "i", $child['id']);
                            mysqli_stmt_execute($stmt);
                            $attendance_result = mysqli_stmt_get_result($stmt);
                            while ($attendance = mysqli_fetch_assoc($attendance_result)): ?>
                                <li><?php echo htmlspecialchars($attendance['schedule_date'] . ' - ' . $attendance['event_type']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
