<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Use password hashing for security
    $email = $_POST['email'];
    $role = $_POST['role'];
    $access_code = $_POST['access_code'];

    // Validate role and access code
    if (!in_array($role, ['admin', 'teacher', 'student', 'parent']) || !preg_match('/^\d{4}$/', $access_code)) {
        $error = "Invalid role or access code. Access code must be exactly 4 digits.";
    } else {
        // Prepare the SQL query
        $query = "INSERT INTO users (username, password, email, role, access_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            $error = "Error preparing statement: " . mysqli_error($conn); // Handle query preparation errors
        } else {
            // Bind parameters and execute the query
            mysqli_stmt_bind_param($stmt, "sssss", $username, $password, $email, $role, $access_code);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php');
                exit;
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up - School Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-form-container">
            <i class="fas fa-user-plus auth-icon"></i>
            <h2>Create Account</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-users"></i>
                    <select name="role" id="role" required class="form-select">
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
                <div id="role-specific-fields"></div>
                <div class="input-group">
                    <i class="fas fa-key"></i>
                    <input type="text" name="access_code" placeholder="4-Digit Access Code" maxlength="4" required>
                </div>
                <button type="submit" class="auth-button">Sign Up</button>
                <p class="auth-links">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
    document.getElementById('role').addEventListener('change', function() {
        const roleFields = document.getElementById('role-specific-fields');
        const role = this.value;
        let fields = '';
        
        switch(role) {
            case 'student':
                fields = `<div class="input-group">
                            <i class="fas fa-graduation-cap"></i>
                            <input type="text" name="class" placeholder="Class" required>
                         </div>`;
                break;
            case 'parent':
                fields = `<div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone" placeholder="Phone Number" required>
                         </div>`;
                break;
            case 'admin':
                fields = ``;
                break;
            case 'teacher':
                fields = `<div class="input-group">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <input type="text" name="subject" placeholder="Subject" required>
                         </div>`;
                break;
        }
        roleFields.innerHTML = fields;
    });
    </script>
</body>
</html>
