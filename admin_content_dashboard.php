<?php
// Include database connection
include 'db_connection.php';

// Fetch counts for students, teachers, parents, and admins
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"))['count'];
$teacher_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM teachers"))['count'];
$parent_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parents"))['count'];
$admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"))['count'];
?>

<div class="statistics-container">
    <h3>Statistics</h3>
    <ul>
        <li>Students: <?php echo $student_count; ?></li>
        <li>Teachers: <?php echo $teacher_count; ?></li>
        <li>Parents: <?php echo $parent_count; ?></li>
        <li>Admins: <?php echo $admin_count; ?></li>
    </ul>
</div>
