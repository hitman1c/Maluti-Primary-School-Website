<?php
session_start();

// Include the database connection file
$connection_file = __DIR__ . '/db_connection.php';
if (file_exists($connection_file)) {
    include $connection_file;
} else {
    die("Error: Database connection file not found.");
}

// Check if user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: unauthorized.php');
    exit;
}

$message = '';

// Handle password update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = $_POST['new_password'];

    if (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the password in the database
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Password updated successfully for user ID $user_id.";
        } else {
            $message = "Error updating password: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all users
$query = "SELECT id, username, password FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Manage Passwords</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
            font-family: monospace;
        }
        th {
            background-color: #eee;
        }
        form {
            margin: 0;
        }
        input[type="password"] {
            width: 150px;
            padding: 5px;
            margin-right: 5px;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 3px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin: 10px 0;
            color: green;
        }
        .error {
            color: red;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Admin Manage Passwords</h1>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Password Hash</th>
                <th>Set New Password</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><code><?= htmlspecialchars($row['password']) ?></code></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to update the password for user ID <?= $row['id'] ?>?');">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>" />
                                <input type="password" name="new_password" placeholder="New password" required minlength="6" />
                                <input type="submit" value="Update" />
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
