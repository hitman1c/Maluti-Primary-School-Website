<?php
session_start();
require_once "config.php";

if ($_SESSION["role"] !== "student") {
    header("location: login.php");
    exit;
}

if (isset($_POST['submit_feedback'])) {
    $assignment_id = $_POST['assignment_id'];
    $feedback = $_POST['feedback'];

    $sql = "INSERT INTO feedback (assignment_id, student_id, feedback) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $assignment_id, $_SESSION["user_id"], $feedback);
    $stmt->execute();

    header("location: student_dashboard.php");
    exit;
}
?>
