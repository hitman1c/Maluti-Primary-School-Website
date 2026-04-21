<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'teacher') {
    // Fetch all notifications for teachers
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Fetch notifications for the logged-in user
    $sql = "SELECT * FROM notifications WHERE recipient_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .notification {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification:last-child {
            border-bottom: none;
        }
        .notification-content {
            flex: 1;
            margin-right: 20px;
        }
        .notification-date {
            color: #888;
            font-size: 14px;
            white-space: nowrap;
        }
        .no-notifications {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Your Notifications</h2>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($notification = mysqli_fetch_assoc($result)): ?>
                <div class="notification">
                    <div class="notification-content">
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </div>
                    <div class="notification-date">
                        <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-notifications">You have no notifications.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
