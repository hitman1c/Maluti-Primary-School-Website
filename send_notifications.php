<?php
include 'auth.php';

// Allow all logged-in users to send notifications
session_start();

require_once "config.php";

$message = '';
$error = '';
$recipient_type = '';
$recipients = [];

// Get logged-in user info for sender
$sender_id = $_SESSION['user_id'] ?? null;
$sender_email = '';
$sender_name = '';

// Fetch sender email and name
if ($sender_id) {
    $res = mysqli_query($conn, "SELECT u.email, 
        COALESCE(t.first_name, p.first_name, s.first_name, u.username) AS first_name,
        COALESCE(t.last_name, p.last_name, s.last_name, '') AS last_name
        FROM users u
        LEFT JOIN teachers t ON u.id = t.user_id
        LEFT JOIN parents p ON u.id = p.user_id
        LEFT JOIN students s ON u.id = s.user_id
        WHERE u.id = $sender_id LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
        $sender_email = $row['email'];
        $sender_name = trim($row['first_name'] . ' ' . $row['last_name']);
        if (empty($sender_name)) {
            $sender_name = $sender_email;
        }
    }
}

// Fetch all users grouped by role for dropdowns
$teachers_result = mysqli_query($conn, "SELECT t.id, t.first_name, t.last_name, u.email FROM teachers t JOIN users u ON t.user_id = u.id ORDER BY t.first_name, t.last_name");
$parents_result = mysqli_query($conn, "SELECT p.id, p.first_name, p.last_name, u.email FROM parents p JOIN users u ON p.user_id = u.id ORDER BY p.first_name, p.last_name");
$students_result = mysqli_query($conn, "SELECT s.id, s.first_name, s.last_name, u.email FROM students s JOIN users u ON s.user_id = u.id ORDER BY s.first_name, s.last_name");
$admins_result = mysqli_query($conn, "SELECT id, username, email FROM users WHERE role = 'admin' ORDER BY username");

if(isset($_POST['send_notification'])) {
    $recipient_type = $_POST['recipient_type'] ?? '';
    $selected_recipients = $_POST['recipients'] ?? [];
    $notification_type = $_POST['notification_type'] ?? '';
    $notification_message = trim($_POST['message'] ?? '');

    if(empty($recipient_type) || empty($notification_type) || empty($notification_message)) {
        $error = "Please fill in all required fields.";
    } else {
        // Determine recipients based on selection
        if($recipient_type === 'all') {
            // Send to all users
            $recipients = [];
            $roles = ['teacher', 'parent', 'student', 'admin'];
            foreach($roles as $role) {
                $res = mysqli_query($conn, "SELECT u.email FROM users u WHERE u.role = '$role' AND u.status = 'active'");
                while($row = mysqli_fetch_assoc($res)) {
                    $recipients[] = $row['email'];
                }
            }
        } else {
            // Send to selected recipients
            if(empty($selected_recipients)) {
                $error = "Please select at least one recipient.";
            } else {
                // Fetch emails of selected recipients based on recipient_type
                $table_map = [
                    'teacher' => ['table' => 'teachers', 'user_table' => 'users', 'id_field' => 't.id', 'user_id_field' => 't.user_id'],
                    'parent' => ['table' => 'parents', 'user_table' => 'users', 'id_field' => 'p.id', 'user_id_field' => 'p.user_id'],
                    'student' => ['table' => 'students', 'user_table' => 'users', 'id_field' => 's.id', 'user_id_field' => 's.user_id'],
                    'admin' => ['table' => 'users', 'user_table' => '', 'id_field' => 'id', 'user_id_field' => '']
                ];
                $map = $table_map[$recipient_type] ?? null;
                if($map) {
                    $ids = implode(',', array_map('intval', $selected_recipients));
                    if($recipient_type === 'admin') {
                        $query = "SELECT email FROM users WHERE id IN ($ids) AND role = 'admin' AND status = 'active'";
                    } else {
                        $query = "SELECT u.email FROM {$map['table']} {$recipient_type[0]} JOIN users u ON {$recipient_type[0]}.user_id = u.id WHERE {$recipient_type[0]}.id IN ($ids) AND u.status = 'active'";
                    }
                    $res = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($res)) {
                        $recipients[] = $row['email'];
                    }
                }
            }
        }

        if(empty($error)) {
            $subject = "Notification from " . htmlspecialchars($sender_name);
            $body = $notification_message;
            $headers = "From: " . htmlspecialchars($sender_name) . " <" . htmlspecialchars($sender_email) . ">\r\n";

            if($notification_type === 'email') {
                $success_count = 0;
                foreach($recipients as $email) {
                    if(mail($email, $subject, $body, $headers)) {
                        $success_count++;
                    }
                }
                if($success_count === count($recipients)) {
                    $message = "Email notification sent successfully to all recipients.";
                } elseif($success_count > 0) {
                    $message = "Email notification sent to some recipients.";
                } else {
                    $error = "Failed to send email notification.";
                }
            } elseif($notification_type === 'shortnotice') {
                // Simulate short notice sending
                $message = "Short notice sent successfully (simulation).";
            } else {
                $error = "Invalid notification type.";
            }

            // Log notifications
            foreach($recipients as $email) {
                $stmt = mysqli_prepare($conn, "INSERT INTO notifications (teacher_id, message) VALUES (?, ?)");
                // For logging, find user id by email
                $user_res = mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
                $user = mysqli_fetch_assoc($user_res);
                $user_id = $user['id'] ?? null;
                if($user_id) {
                    mysqli_stmt_bind_param($stmt, "is", $user_id, $notification_message);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Notifications</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Card-based layout and styling */
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 28px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            resize: vertical;
        }
        select:focus, textarea:focus {
            border-color: #4e73df;
            outline: none;
        }
        button {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            width: 100%;
        }
        button:hover {
            background-color: #2e59d9;
        }
        .success-message, .error-message {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 16px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1.5px solid #f5c6cb;
        }
        #recipients_div {
            margin-top: 15px;
        }
    </style>
    <script>
        function loadRecipients() {
            var recipientType = document.getElementById('recipient_type').value;
            var recipientsDiv = document.getElementById('recipients_div');
            var recipientsSelect = document.getElementById('recipients');
            recipientsSelect.innerHTML = '';
            if(recipientType === '') {
                recipientsDiv.style.display = 'none';
                return;
            }
            recipientsDiv.style.display = 'block';

            var recipientsData = {
                teachers: <?php echo json_encode(mysqli_fetch_all($teachers_result, MYSQLI_ASSOC)); ?>,
                parents: <?php echo json_encode(mysqli_fetch_all($parents_result, MYSQLI_ASSOC)); ?>,
                students: <?php echo json_encode(mysqli_fetch_all($students_result, MYSQLI_ASSOC)); ?>,
                admins: <?php echo json_encode(mysqli_fetch_all($admins_result, MYSQLI_ASSOC)); ?>
            };

            var list = recipientsData[recipientType + 's'] || [];
            list.forEach(function(user) {
                var option = document.createElement('option');
                option.value = user.id;
                option.text = user.first_name ? (user.first_name + ' ' + user.last_name) : user.username;
                recipientsSelect.appendChild(option);
            });
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Send Notification</h2>
        <?php if($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="recipient_type">Recipient Type:</label>
            <select name="recipient_type" id="recipient_type" onchange="loadRecipients()" required>
                <option value="">-- Select Recipient Type --</option>
                <option value="teacher" <?php if($recipient_type === 'teacher') echo 'selected'; ?>>Teacher</option>
                <option value="parent" <?php if($recipient_type === 'parent') echo 'selected'; ?>>Parent</option>
                <option value="student" <?php if($recipient_type === 'student') echo 'selected'; ?>>Student</option>
                <option value="admin" <?php if($recipient_type === 'admin') echo 'selected'; ?>>Admin</option>
                <option value="all" <?php if($recipient_type === 'all') echo 'selected'; ?>>All</option>
            </select>
            <div id="recipients_div" style="display:none; margin-top: 15px;">
                <label for="recipients">Select Recipients (Ctrl+Click for multiple):</label><br>
                <select name="recipients[]" id="recipients" multiple size="10" style="width: 100%; border-radius: 6px; border: 1.5px solid #ddd; padding: 10px;">
                </select>
            </div>
            <label for="notification_type" style="margin-top: 20px;">Notification Type:</label>
            <select name="notification_type" id="notification_type" required>
                <option value="">-- Select Type --</option>
                <option value="email" <?php if(isset($_POST['notification_type']) && $_POST['notification_type'] === 'email') echo 'selected'; ?>>Email</option>
                <option value="shortnotice" <?php if(isset($_POST['notification_type']) && $_POST['notification_type'] === 'shortnotice') echo 'selected'; ?>>Short Notice</option>
            </select>
            <label for="message" style="margin-top: 20px;">Message:</label>
            <textarea name="message" id="message" rows="6" required style="width: 100%; border-radius: 6px; border: 1.5px solid #ddd; padding: 10px; font-size: 16px;"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            <button type="submit" name="send_notification">Send Notification</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
