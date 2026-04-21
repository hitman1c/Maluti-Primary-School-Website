<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get user data based on role
switch($role) {
    case 'admin':
        $sql = "SELECT * FROM users WHERE id = ?";
        break;
    case 'student':
        $sql = "SELECT s.*, u.email, u.username 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.user_id = ?";
        break;
    case 'teacher':
        $sql = "SELECT t.*, u.email, u.username 
                FROM teachers t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.user_id = ?";
        break;
    case 'parent':
        $sql = "SELECT p.*, u.email, u.username 
                FROM parents p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = ?";
        break;
}

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($userData = mysqli_fetch_assoc($result)) {
    // User data found, continue with dashboard display
} else {
    die("Error: Please complete your profile registration");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
            <h3><?php echo htmlspecialchars($userData['username'] ?? ($_SESSION['username'] ?? 'User')); ?></h3>
            <p><?php echo ucfirst($_SESSION['role']); ?></p>
            </div>
            <?php include "nav_" . $_SESSION['role'] . ".php"; ?>
        </div>
        <div class="main-content">
            <?php
            $role = $_SESSION['role'];
            switch ($role) {
                case 'admin':
                    include 'admin_dashboard.php';
                    break;
                case 'teacher':
                    include 'teacher_dashboard.php';
                    break;
                case 'student':
                    include 'student_dashboard.php';
                    break;
                case 'parent':
                    include 'parent_dashboard.php';
                    break;
                default:
                    echo "<p>Dashboard content not available for your role.</p>";
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>
