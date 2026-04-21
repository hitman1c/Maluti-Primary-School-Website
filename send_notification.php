<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Handle the notification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role']; // Use the user's role for the notification

    // Insert the notification into the database
    $notification_query = "INSERT INTO notifications (message, user_id, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $notification_query);
    mysqli_stmt_bind_param($stmt, "sis", $message, $user_id, $role);
    mysqli_stmt_execute($stmt);

    // Redirect back to the home page
    header('Location: index.php');
    exit;
}
?>
