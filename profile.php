<?php
include 'auth.php';
require_once "config.php";

if(!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user data for display and editing
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($result);

// Handle form submissions for different tasks here (to be implemented)

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
            padding: 40px 50px;
            color: white;
        }
        h2 {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            font-weight: 700;
            font-size: 32px;
            letter-spacing: 1.2px;
        }
        .tabs {
            display: flex;
            border-bottom: 3px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .tab {
            padding: 14px 28px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0 8px 8px 8px;
            border-radius: 12px 12px 0 0;
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .tab.active {
            border: 1px solid white;
            border-bottom: 3px solid #764ba2;
            background-color: white;
            color: #764ba2;
            font-weight: 700;
            box-shadow: 0 6px 12px rgba(118, 75, 162, 0.5);
        }
        .tab:hover {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
            color: #333;
            background-color: white;
            border-radius: 0 12px 12px 12px;
            padding: 30px 40px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        textarea:focus,
        select:focus {
            border-color: #764ba2;
            outline: none;
            box-shadow: 0 0 8px rgba(118, 75, 162, 0.5);
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            font-weight: 700;
            letter-spacing: 1px;
        }
        button:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .success-message, .error-message {
            padding: 14px 24px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
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
    </style>
    <script>
        function showTab(tabId) {
            var tabs = document.querySelectorAll('.tab');
            var contents = document.querySelectorAll('.tab-content');
            tabs.forEach(function(tab) {
                tab.classList.remove('active');
            });
            contents.forEach(function(content) {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            document.querySelector('[data-tab="' + tabId + '"]').classList.add('active');
        }
        document.addEventListener('DOMContentLoaded', function() {
            showTab('tab1');
            var tabs = document.querySelectorAll('.tab');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    showTab(this.getAttribute('data-tab'));
                });
            });
        });
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>User Profile</h2>
        <div class="tabs">
            <div class="tab" data-tab="tab1">Edit Personal Information</div>
            <div class="tab" data-tab="tab2">Change Password</div>
            <div class="tab" data-tab="tab3">Upload Profile Picture</div>
            <div class="tab" data-tab="tab4">View Activity Logs</div>
            <div class="tab" data-tab="tab5">Set Preferences</div>
            <div class="tab" data-tab="tab6">Manage Linked Accounts</div>
            <div class="tab" data-tab="tab7">Update Contact Information</div>
            <div class="tab" data-tab="tab8">Access Reports & Documents</div>
            <div class="tab" data-tab="tab9">Modify Security Settings</div>
            <div class="tab" data-tab="tab10">Delete or Deactivate Account</div>
        </div>
        <div id="tab1" class="tab-content">
            <form method="post" action="profile_update.php">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                <button type="submit" name="update_personal_info">Update Information</button>
            </form>
        </div>
        <div id="tab2" class="tab-content">
            <form method="post" action="change_password.php">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
        <div id="tab3" class="tab-content">
            <form method="post" action="upload_profile_picture.php" enctype="multipart/form-data">
                <label for="profile_picture">Upload Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                <button type="submit" name="upload_picture">Upload</button>
            </form>
        </div>
        <div id="tab4" class="tab-content">
            <p>Activity logs will be displayed here.</p>
        </div>
        <div id="tab5" class="tab-content">
            <p>Preferences settings will be available here.</p>
        </div>
        <div id="tab6" class="tab-content">
            <p>Linked accounts management will be available here.</p>
        </div>
        <div id="tab7" class="tab-content">
            <form method="post" action="update_contact_info.php">
                <label for="emergency_contact">Emergency Contact</label>
                <input type="text" id="emergency_contact" name="emergency_contact" value="<?php echo htmlspecialchars($userData['emergency_contact'] ?? ''); ?>">
                <label for="secondary_email">Secondary Email</label>
                <input type="email" id="secondary_email" name="secondary_email" value="<?php echo htmlspecialchars($userData['secondary_email'] ?? ''); ?>">
                <button type="submit" name="update_contact_info">Update Contact Information</button>
            </form>
        </div>
        <div id="tab8" class="tab-content">
            <p>Reports and documents will be accessible here.</p>
        </div>
        <div id="tab9" class="tab-content">
            <p>Security settings modification will be available here.</p>
        </div>
        <div id="tab10" class="tab-content">
            <p>Account deletion or deactivation options will be available here.</p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
