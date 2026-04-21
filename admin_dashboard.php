<?php
include 'auth.php';
include 'db_connection.php';
checkRoleWithCode('admin'); // Restrict access to admin users with a valid 4-digit code

$admin_id = $_SESSION['user_id'] ?? null;
$adminData = null;

if ($admin_id) {
    $admin_query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $admin_query);
    mysqli_stmt_bind_param($stmt, "i", $admin_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $adminData = mysqli_fetch_assoc($result);
}

$admin_name = htmlspecialchars($adminData['name'] ?? 'Admin');
?>
<style>
    .dashboard-header {
        width: 100%;
        background-color: #007bff;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 1.8em;
        font-weight: bold;
    }

    .welcome {
        margin: 10px 0;
        font-size: 1.2em;
        color: #333;
    }

    .dashboard-cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: flex-start;
        padding: 20px;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        flex: 1 1 calc(33.333% - 40px);
        min-width: 300px;
        max-width: 400px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    .card h3 {
        margin-top: 0;
        font-size: 1.5em;
        color: #333;
    }

    .list, .table {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>
<div>
    <div class="dashboard-header">Admin Dashboard</div>
    <div class="welcome">Welcome, Admin <?php echo $admin_name; ?>! What is your work today?</div>
    <div class="action-buttons">
        <a href="logout.php" class="btn">Logout</a>
    </div>
    <div class="dashboard-cards">
        <div class="card">
            <h3>Statistics</h3>
            <div class="stats-grid" style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-around;">
                <?php
                // Fetch counts for students, teachers, parents, and admins
                $student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"))['count'];
                $teacher_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM teachers"))['count'];
                $parent_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parents"))['count'];
                $admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"))['count'];
                ?>
                <div style="background: #1cc88a; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                    <h4>Students</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $student_count; ?></p>
                </div>
                <div style="background: #36b9cc; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                    <h4>Teachers</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $teacher_count; ?></p>
                </div>
                <div style="background: #f6c23e; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                    <h4>Parents</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $parent_count; ?></p>
                </div>
                <div style="background: #e74a3b; color: white; padding: 20px; border-radius: 8px; flex: 1 1 150px; text-align: center;">
                    <h4>Admins</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo $admin_count; ?></p>
                </div>
            </div>
        </div>
        <div class="card">
            <h3>Manage Teachers</h3>
            <div class="list">
                <?php
                $teachers_query = "SELECT id, first_name, last_name FROM teachers";
                $teachers_result = mysqli_query($conn, $teachers_query);
                if (mysqli_num_rows($teachers_result) > 0) {
                    echo "<ul>";
                    while ($teacher = mysqli_fetch_assoc($teachers_result)) {
                        echo "<li>" . htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No teachers available.</p>";
                }
                ?>
            </div>
        </div>

        <div class="card">
            <h3>Manage Students</h3>
            <div class="list">
                <?php
                $students_query = "SELECT id, first_name, last_name FROM students";
                $students_result = mysqli_query($conn, $students_query);
                if (mysqli_num_rows($students_result) > 0) {
                    echo "<ul>";
                    while ($student = mysqli_fetch_assoc($students_result)) {
                        echo "<li>" . htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No students available.</p>";
                }
                ?>
            </div>
        </div>

        <div class="card">
            <h3>Manage Classes</h3>
            <div class="list">
                <?php
                $classes_query = "SELECT id, name FROM classes";
                $classes_result = mysqli_query($conn, $classes_query);
                if (mysqli_num_rows($classes_result) > 0) {
                    echo "<ul>";
                    while ($class = mysqli_fetch_assoc($classes_result)) {
                        echo "<li>" . htmlspecialchars($class['name']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No classes available.</p>";
                }
                ?>
            </div>
        </div>

        <div class="card">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="manage_teachers.php" class="btn">Manage Teachers</a>
                <a href="manage_students.php" class="btn">Manage Students</a>
                <a href="manage_classes.php" class="btn">Manage Classes</a>
            </div>
        </div>

        <div class="card">
            <h3>Recent Updates</h3>
            <?php
            $updates_query = "SELECT message, created_at FROM notifications ORDER BY created_at DESC LIMIT 5";
            $updates_result = mysqli_query($conn, $updates_query);
            if (mysqli_num_rows($updates_result) > 0) {
                while ($update = mysqli_fetch_assoc($updates_result)) {
                    echo "<div class='update-item'>"; 
                    echo "<p>" . htmlspecialchars($update['message']) . "</p>";
                    echo "<small>" . date('M d, Y', strtotime($update['created_at'])) . "</small>";
                    echo "</div>";
                }
            } else {
                echo "<p>No updates available.</p>";
            }
            ?>
        </div>
    </div>
</div>
