<?php
session_start();
require_once "config.php";
if($_SESSION["role"] !== "student") {
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <i class="fas fa-user-graduate card-icon"></i>
            <h3>My Profile</h3>
            <?php
            $sql = "SELECT * FROM students WHERE user_id = " . $_SESSION['id'];
            $result = mysqli_query($conn, $sql);
            $student = mysqli_fetch_assoc($result);
            echo "<p>Name: " . $student['first_name'] . " " . $student['last_name'] . "</p>";
            echo "<p>Class: " . $student['class'] . "</p>";
            ?>
        </div>
            <div class="dashboard-card">
                <i class="fas fa-chart-line card-icon"></i>
                <h3>My Grades</h3>
                <div class="grades-container">
                    <?php
                    $student_id = $userData['id'];
                    $grades_sql = "SELECT a.title, g.grade FROM grades g JOIN assignments a ON g.assignment_id = a.id WHERE g.student_id = ?";
                    $stmt = mysqli_prepare($conn, $grades_sql);
                    mysqli_stmt_bind_param($stmt, "i", $student_id);
                    mysqli_stmt_execute($stmt);
                    $grades_result = mysqli_stmt_get_result($stmt);
                    if(mysqli_num_rows($grades_result) > 0) {
                        echo "<ul>";
                        while($grade = mysqli_fetch_assoc($grades_result)) {
                            echo "<li>" . htmlspecialchars($grade['title']) . ": " . htmlspecialchars($grade['grade']) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No grades available.</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-calendar-check card-icon"></i>
                <h3>My Attendance</h3>
                <!-- Add attendance stats -->
            </div>
            <div class="dashboard-card">
                <i class="fas fa-tasks card-icon"></i>
                <h3>My Assignments</h3>
                <div class="assignments-container">
                    <?php
                    $class = $userData['class'];
                    $assignments_sql = "SELECT title, due_date FROM assignments WHERE class = ?";
                    $stmt = mysqli_prepare($conn, $assignments_sql);
                    mysqli_stmt_bind_param($stmt, "s", $class);
                    mysqli_stmt_execute($stmt);
                    $assignments_result = mysqli_stmt_get_result($stmt);
                    if(mysqli_num_rows($assignments_result) > 0) {
                        echo "<ul>";
                        while($assignment = mysqli_fetch_assoc($assignments_result)) {
                            echo "<li>" . htmlspecialchars($assignment['title']) . " - Due: " . htmlspecialchars($assignment['due_date']) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No assignments available.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
</body>
</html>
