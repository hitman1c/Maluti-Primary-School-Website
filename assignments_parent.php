<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

// Fetch assignments for the parent's child
$child_class_query = "SELECT class FROM students WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $child_class_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['child_user_id']);
mysqli_stmt_execute($stmt);
$class_result = mysqli_stmt_get_result($stmt);
$class = mysqli_fetch_assoc($class_result)['class'];

$assignments_query = "SELECT title, description, due_date FROM assignments WHERE class_id = ?";
$stmt = mysqli_prepare($conn, $assignments_query);
mysqli_stmt_bind_param($stmt, "s", $class);
mysqli_stmt_execute($stmt);
$assignments_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent - View Assignments</title>
</head>
<body>
    <h1>Assignments for Your Child</h1>
    <ul>
        <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
            <li>
                <strong><?php echo htmlspecialchars($assignment['title']); ?></strong><br>
                <?php echo htmlspecialchars($assignment['description']); ?><br>
                <em>Due: <?php echo htmlspecialchars($assignment['due_date']); ?></em>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
