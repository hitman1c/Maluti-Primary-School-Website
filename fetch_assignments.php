<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Fetch assignments for the student's classes
$query = "SELECT a.title, a.description, a.due_date, c.name AS class_name 
          FROM assignments a
          JOIN classes c ON a.class_id = c.id
          JOIN student_classes sc ON sc.class_id = c.id
          WHERE sc.student_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);

$assignments = [];
while ($row = mysqli_fetch_assoc($results)) {
    $assignments[] = $row;
}

echo json_encode($assignments);
?>
