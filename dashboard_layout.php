<?php
// dashboard_layout.php
// Common dashboard layout template with sidebar, topbar, main content, and popup system

if(!isset($_SESSION['loggedin'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user data for display
require_once "config.php";
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($result);

// Define sidebar tabs per role
$sidebar_tabs = [
    'admin' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['id' => 'students', 'label' => 'Students', 'icon' => 'fas fa-user-graduate'],
        ['id' => 'teachers', 'label' => 'Teachers', 'icon' => 'fas fa-chalkboard-teacher'],
        ['id' => 'classes', 'label' => 'Classes', 'icon' => 'fas fa-school'],
        ['id' => 'fees', 'label' => 'Fees', 'icon' => 'fas fa-dollar-sign'],
        ['id' => 'reports', 'label' => 'Reports', 'icon' => 'fas fa-chart-bar'],
        ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'fas fa-bell'],
        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'fas fa-cogs'],
    ],
    'teacher' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['id' => 'attendance', 'label' => 'Attendance', 'icon' => 'fas fa-clipboard-check'],
        ['id' => 'exams', 'label' => 'Exams', 'icon' => 'fas fa-file-alt'],
        ['id' => 'classes', 'label' => 'Classes', 'icon' => 'fas fa-school'],
        ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'fas fa-bell'],
        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'fas fa-cogs'],
    ],
    'student' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['id' => 'attendance', 'label' => 'Attendance', 'icon' => 'fas fa-clipboard-check'],
        ['id' => 'exams', 'label' => 'Exams', 'icon' => 'fas fa-file-alt'],
        ['id' => 'fees', 'label' => 'Fees', 'icon' => 'fas fa-dollar-sign'],
        ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'fas fa-bell'],
        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'fas fa-cogs'],
    ],
    'parent' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['id' => 'children', 'label' => 'Children', 'icon' => 'fas fa-child'],
        ['id' => 'attendance', 'label' => 'Attendance', 'icon' => 'fas fa-clipboard-check'],
        ['id' => 'fees', 'label' => 'Fees', 'icon' => 'fas fa-dollar-sign'],
        ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'fas fa-bell'],
        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'fas fa-cogs'],
    ],
];

// Determine selected tab from GET or default to dashboard
$selected_tab = $_GET['tab'] ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo ucfirst($role); ?> Dashboard - Maluti Primary School</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
        }
        .dashboard-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        /* Sidebar */
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        .sidebar .logo {
            font-size: 1.5em;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1.5px;
        }
        .sidebar nav {
            flex-grow: 1;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #bdc3c7;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-left: 4px solid transparent;
        }
        .sidebar nav a:hover,
        .sidebar nav a.active {
            background-color: #34495e;
            color: #1abc9c;
            border-left: 4px solid #1abc9c;
        }
        .sidebar nav a i {
            margin-right: 12px;
            font-size: 1.2em;
        }
        /* Topbar */
        .topbar {
            height: 60px;
            background: white;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .topbar .search-bar {
            flex-grow: 1;
            max-width: 400px;
        }
        .topbar .search-bar input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .topbar .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .topbar .user-menu .user-name {
            font-weight: 600;
            color: #333;
        }
        .topbar .user-menu .user-avatar {
            cursor: pointer;
            font-size: 1.8em;
            color: #555;
        }
        /* Main content */
        .main-content {
            flex-grow: 1;
            padding: 20px 30px;
            overflow-y: auto;
            background: #fff;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(180px,1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #1abc9c;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.3);
            text-align: center;
            font-weight: 700;
            font-size: 1.2em;
        }
        /* Quick actions popup */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup {
            background: white;
            border-radius: 12px;
            width: 600px;
            max-width: 90%;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
        }
        .popup .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
        }
        /* Buttons for quick actions */
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }
        .quick-actions button {
            flex: 1 1 150px;
            padding: 15px 20px;
            background: #1abc9c;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .quick-actions button:hover {
            background: #16a085;
        }
    </style>
    <script>
        function openPopup(popupId) {
            document.getElementById('popupOverlay').style.display = 'flex';
            document.getElementById(popupId).style.display = 'block';
        }
        function closePopup(popupId) {
            document.getElementById('popupOverlay').style.display = 'none';
            document.getElementById(popupId).style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="logo">Maluti Primary School</div>
            <nav>
                <?php foreach($sidebar_tabs[$role] as $tab): ?>
                    <a href="?tab=<?php echo $tab['id']; ?>" class="<?php echo ($selected_tab === $tab['id']) ? 'active' : ''; ?>">
                        <i class="<?php echo $tab['icon']; ?>"></i> <?php echo $tab['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <div class="main-section">
            <header class="topbar">
                <div class="search-bar">
                    <input type="text" placeholder="Search..." />
                </div>
                <div class="user-menu">
                    <span class="user-name"><?php echo htmlspecialchars($userData['username'] ?? 'User'); ?></span>
                    <i class="fas fa-user-circle user-avatar" title="Profile"></i>
                </div>
            </header>
            <main class="main-content">
                <?php
                // Load content based on selected tab and role
                $content_file = "dashboard_content_{$role}.php";
                if(file_exists($content_file)) {
                    include $content_file;
                } else {
                    echo "<p>Content not available.</p>";
                }
                ?>
            </main>
        </div>
    </div>

    <!-- Popup overlay -->
    <div id="popupOverlay" class="popup-overlay" onclick="closePopup('popupContent')"></div>

    <!-- Example popup for quick actions -->
    <div id="popupContent" class="popup" style="display:none;">
        <button class="close-btn" onclick="closePopup('popupContent')">&times;</button>
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <button onclick="alert('Add Student')">Add Student</button>
            <button onclick="alert('Manage Teachers')">Manage Teachers</button>
            <button onclick="alert('Mark Attendance')">Mark Attendance</button>
            <button onclick="alert('Generate Reports')">Generate Reports</button>
            <button onclick="alert('Send Notifications')">Send Notifications</button>
        </div>
    </div>
</body>
</html>
