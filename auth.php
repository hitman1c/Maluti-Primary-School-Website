<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'db_connection.php';

if (!function_exists('checkAuth')) {
    // Function to check if the user is logged in
    function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }
}

if (!function_exists('checkRole')) {
    // Function to check role
    function checkRole($role) {
        checkAuth();
        if ($_SESSION['role'] !== $role) {
            die("Access denied: You do not have the required role.");
        }
    }
}

if (!function_exists('checkRoleWithCode')) {
    // Function to check role and access code
    function checkRoleWithCode($role) {
        global $conn;

        // Ensure the user is logged in
        checkAuth();

        // Debugging: Log the current session role
        if (!isset($_SESSION['role'])) {
            die("Access denied: Role is not set in the session.");
        }
        if ($_SESSION['role'] !== $role) {
            die("Access denied: You do not have the required role. Your role: " . htmlspecialchars($_SESSION['role']));
        }

        // Check if the user has a valid access code
        $user_id = $_SESSION['user_id'];
        $access_code_query = "SELECT access_code FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $access_code_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user || strlen($user['access_code']) !== 4) {
            die("Access denied: Invalid or missing access code.");
        }
    }
}
?>
