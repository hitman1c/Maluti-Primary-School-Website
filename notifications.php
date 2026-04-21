<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission for sending a notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $message = $_POST['message'];

    // Insert notification into the database
    $insert_query = "INSERT INTO notifications (parent_id, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $message);
    mysqli_stmt_execute($stmt);

    echo "Notification sent successfully!";
}

// Handle form submission for replying to a notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $notification_id = $_POST['notification_id'];
    $reply_message = $_POST['reply_message'];

    // Insert reply into the database
    $insert_reply_query = "INSERT INTO replies (notification_id, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_reply_query);
    mysqli_stmt_bind_param($stmt, "is", $notification_id, $reply_message);
    mysqli_stmt_execute($stmt);

    echo "Reply sent successfully!";
}

// Fetch notifications
$notifications_query = "SELECT id, message, created_at FROM notifications ORDER BY created_at DESC LIMIT 10";
$notifications_result = mysqli_query($conn, $notifications_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fc;
        }

        ul li p {
            margin: 0;
        }

        ul li small {
            display: block;
            margin-top: 5px;
            color: #666;
        }

        form {
            margin-top: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .reply-section {
            margin-top: 10px;
            padding-left: 20px;
        }

        .reply-section p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Notifications</h2>
        <ul>
            <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                <li>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></small>
                    <div class="reply-section">
                        <h4>Replies:</h4>
                        <?php
                        $replies_query = "SELECT message, created_at FROM replies WHERE notification_id = ?";
                        $stmt = mysqli_prepare($conn, $replies_query);
                        mysqli_stmt_bind_param($stmt, "i", $notification['id']);
                        mysqli_stmt_execute($stmt);
                        $replies_result = mysqli_stmt_get_result($stmt);
                        while ($reply = mysqli_fetch_assoc($replies_result)): ?>
                            <p><?php echo htmlspecialchars($reply['message']); ?> <small>(<?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?>)</small></p>
                        <?php endwhile; ?>
                        <form method="POST">
                            <textarea name="reply_message" rows="2" placeholder="Write a reply..." required></textarea>
                            <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                            <button type="submit" name="send_reply">Reply</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
        <form method="POST">
            <textarea name="message" rows="4" placeholder="Write a new notification..." required></textarea>
            <button type="submit" name="send_notification">Send Notification</button>
        </form>
    </div>
</body>
</html>
