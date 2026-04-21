<?php
function getUserData($conn, $user_id, $role) {
    $table = $role . 's'; // students, teachers, parents
    $sql = "SELECT * FROM $table WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

function sendNotification($conn, $user_id, $message, $role) {
    $sql = "INSERT INTO notifications ({$role}_id, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
    return mysqli_stmt_execute($stmt);
}

function getRecentNotifications($conn, $user_id, $role, $limit = 5) {
    $sql = "SELECT * FROM notifications WHERE {$role}_id = ? ORDER BY created_at DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
