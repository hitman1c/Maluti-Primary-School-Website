<?php
header('Content-Type: application/json');
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'];
$grade = $data['grade'];
$attendance = $data['attendance'];

// Insert grade
$grade_query = "INSERT INTO grades (student_id, grade) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE grade = ?";
$stmt = mysqli_prepare($conn, $grade_query);
mysqli_stmt_bind_param($stmt, "isi", $student_id, $grade, $grade);
$grade_result = mysqli_stmt_execute($stmt);

// Insert attendance
$attendance_query = "INSERT INTO attendance (student_id, date, status) VALUES (?, CURDATE(), ?) 
                     ON DUPLICATE KEY UPDATE status = ?";
$stmt = mysqli_prepare($conn, $attendance_query);
mysqli_stmt_bind_param($stmt, "iss", $student_id, $attendance, $attendance);
$attendance_result = mysqli_stmt_execute($stmt);

if ($grade_result && $attendance_result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
