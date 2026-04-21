<?php
session_start();

// Include the database connection file
$connection_file = __DIR__ . '/db_connection.php';
if (file_exists($connection_file)) {
    include $connection_file;
} else {
    die("Error: Database connection file not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $access_code = $_POST['access_code'];

    // Query to fetch user details
    $query = "SELECT id, role, access_code, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verify user credentials
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['access_code'] = $user['access_code'];
        $_SESSION['loggedin'] = true;

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: dashboard_content_admin.php');
        } elseif ($user['role'] === 'teacher') {
            header('Location: dashboard_content_teacher.php');
        } elseif ($user['role'] === 'student') {
            header('Location: dashboard_content_student.php');
        } elseif ($user['role'] === 'parent') {
            header('Location: dashboard_content_parent.php');
        }
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            background-color: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin: 0 0 20px;
            font-size: 24px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #ffcc00;
            color: black;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #e6b800;
        }
        .login-container .signup-link {
            margin-top: 15px;
        }
        .login-container .signup-link a {
            color: #ffcc00;
            text-decoration: none;
            font-weight: bold;
        }
        .login-container .signup-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        footer {
            background-color: rgba(0, 0, 0, 0.9);
            color: #ffcc00;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }
        footer p {
            display: inline-block;
            animation: scroll 20s linear infinite;
        }
        @keyframes scroll {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="access_code" placeholder="4-Digit Access Code" maxlength="4" required>
            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </div>
    <footer>
        <p>Maluti Primary School: Empowering young minds through quality education and cultural values. Join us for Cultural Day on May 15th, Sports Day on June 10th, and Parents' Meeting on July 5th. Contact us at +266 56171110 for more information about our programs, events, and admissions.</p>
    </footer>
</body>
</html>
