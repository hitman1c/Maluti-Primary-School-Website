<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Fetch all classes
$query = "SELECT c.id AS class_id, c.name AS class_name, c.schedule, c.room_number FROM classes";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);

$classes = [];
while ($row = mysqli_fetch_assoc($results)) {
    $classes[] = [
        'class_id' => $row['class_id'],
        'class_name' => $row['class_name']
    ];
}

echo json_encode($classes);
?>