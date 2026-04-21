<?php
session_start();
require_once "config.php";

if ($_SESSION["role"] !== "parent") {
    header("location: login.php");
    exit;
}

if (isset($_POST['send_email'])) {
    $message = $_POST['message'];
    $to = "teacher@school.com"; // Replace with the teacher's email
    $subject = "Message from Parent";
    $headers = "From: parent@school.com"; // Replace with the parent's email

    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
    header("location: parent_dashboard.php");
    exit;
}
?>
