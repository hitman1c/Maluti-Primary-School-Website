<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Handle form submission for creating assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $class_id = $_POST['class_id'];

    $insert_query = "INSERT INTO assignments (title, description, due_date, class_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $due_date, $class_id);
    mysqli_stmt_execute($stmt);

    echo "Assignment created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher - Assign Assignments</title>
</head>
<body>
    <h1>Create Assignment</h1>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>
        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required><br>
        <label for="class_id">Class ID:</label>
        <input type="text" id="class_id" name="class_id" required><br>
        <button type="submit">Create Assignment</button>
    </form>
</body>
</html>
