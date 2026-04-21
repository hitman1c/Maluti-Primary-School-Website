<?php
session_start();
require_once "config.php";
include 'auth.php';
checkRole('teacher');

if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "teacher"){
    header("location: login.php");
    exit;
}

if(isset($_POST['mark_attendance'])) {
    $date = date('Y-m-d');
    $teacher_id = $_SESSION['user_id'];
    foreach($_POST['attendance'] as $student_id => $status) {
        // Get class_id for the student
        $class_id_query = "SELECT class_id FROM students WHERE id = ?";
        $stmt_class = mysqli_prepare($conn, $class_id_query);
        mysqli_stmt_bind_param($stmt_class, "i", $student_id);
        mysqli_stmt_execute($stmt_class);
        $result_class = mysqli_stmt_get_result($stmt_class);
        $class_data = mysqli_fetch_assoc($result_class);
        $class_id = $class_data ? $class_data['class_id'] : null;

        $sql = "INSERT INTO attendance (student_id, class_id, date, status, recorded_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $student_id, $class_id, $date, $status, $teacher_id);
        mysqli_stmt_execute($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Mark Attendance</h2>
        <form method="post">
            <?php
            $teacher_id = $_SESSION['user_id'];
            // Fetch students for teacher's classes
            $result = mysqli_query($conn, "SELECT s.id, s.first_name, s.last_name FROM students s JOIN classes c ON s.class = c.name WHERE c.teacher_id = $teacher_id");
            while($row = mysqli_fetch_array($result)) {
                echo "<div class='attendance-row'>";
                echo "<label>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</label>";
                echo "<select name='attendance[" . $row['id'] . "]'>";
                echo "<option value='present'>Present</option>";
                echo "<option value='absent'>Absent</option>";
                echo "</select>";
                echo "</div>";
            }
            ?>
            <button type="submit" name="mark_attendance">Submit Attendance</button>
        </form>
    </div>
</body>
</html>
