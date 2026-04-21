<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "auth.php";
include 'db_connection.php';


// Check if the user is logged in and has the teacher role
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    header("location: login.php");
    exit;
}

$teacher_id = $_SESSION["user_id"];
// Fetch classes assigned to the logged-in teacher
$classes_query = "SELECT id, name FROM classes WHERE teacher_id = ?";
$stmt = mysqli_prepare($conn, $classes_query);
mysqli_stmt_bind_param($stmt, "i", $teacher_id);
mysqli_stmt_execute($stmt);
$classes_result = mysqli_stmt_get_result($stmt);

// Fetch schedule for the teacher's classes
$schedule_query = "SELECT s.id, s.class_id, s.schedule_date AS day, s.schedule_time AS start_time, 
                   ADDTIME(s.schedule_time, '01:00:00') AS end_time, c.name AS class_name 
                   FROM schedules s 
                   JOIN classes c ON s.class_id = c.id 
                   WHERE c.teacher_id = ?";
$stmt2 = mysqli_prepare($conn, $schedule_query);
mysqli_stmt_bind_param($stmt2, "i", $teacher_id);
mysqli_stmt_execute($stmt2);
$schedule_result = mysqli_stmt_get_result($stmt2);

// Handle form submission for marking attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $class_id = $_POST['class_id'];
    $student_ids = $_POST['student_ids']; // Array of student IDs marked as present

    foreach ($student_ids as $student_id) {
        $attendance_query = "INSERT INTO attendance (student_id, class_id, date) VALUES (?, ?, CURDATE())";
        $stmt = mysqli_prepare($conn, $attendance_query);
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $class_id);
        mysqli_stmt_execute($stmt);
    }
    echo "Attendance marked successfully!";
}

// Handle form submission for creating assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $class_id = $_POST['class_id'];

    $insert_query = "INSERT INTO assignments (title, description, due_date, class_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $due_date, $class_id);
    mysqli_stmt_execute($stmt);

    echo "Assignment created successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Tab styles */
        .tab {
            overflow: hidden;
            border-bottom: 1px solid #ccc;
            background-color: #f1f1f1;
        }
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 17px;
        }
        .tab button:hover {
            background-color: #ddd;
        }
        .tab button.active {
            background-color: #ccc;
        }
        .tabcontent {
            display: none;
            padding: 20px 12px;
            border: 1px solid #ccc;
            border-top: none;
        }
    </style>
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        window.onload = function() {
            document.getElementsByClassName('tablink')[0].click();
        };
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="dashboard-container">
        <h1>Teacher Dashboard</h1>
        <div class="tab">
            <button class="tablink" onclick="openTab(event, 'StudentManagement')">Student Management</button>
            <button class="tablink" onclick="openTab(event, 'AttendanceManagement')">Attendance Management</button>
            <button class="tablink" onclick="openTab(event, 'ExamsReports')">Exams & Reports</button>
            <button class="tablink" onclick="openTab(event, 'FeeManagement')">Fee Management</button>
            <button class="tablink" onclick="openTab(event, 'Notifications')">Notifications</button>
            <button class="tablink" onclick="openTab(event, 'ReportingAnalytics')">Reporting & Analytics</button>
            <button class="tablink" onclick="openTab(event, 'Classes')">Classes</button>
            <button class="tablink" onclick="openTab(event, 'Assignments')">Assignments</button>
        </div> 
        <div id="StudentManagement" class="tabcontent">
            <?php include 'manage_students.php'; ?>
        </div>
        <div id="AttendanceManagement" class="tabcontent">
            <?php include 'mark_attendance.php'; ?>
        </div>
        <div id="ExamsReports" class="tabcontent">
            <?php include 'grade_management.php'; ?>
        </div>
        <div id="FeeManagement" class="tabcontent">
            <?php include 'fee_management_teacher.php'; ?>
        </div>
        <div id="Notifications" class="tabcontent">
            <?php include 'send_notifications_teacher.php'; ?>
        </div>
        <div id="ReportingAnalytics" class="tabcontent">
            <?php include 'reports_teacher.php'; ?>
        </div>
        <div id="Classes" class="tabcontent">
            <h2>Available Classes</h2>
            <ul>
                <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($class['name']); ?></strong>
                        <form method="GET" action="teacher_dashboard.php">
                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                            <button type="submit">View Students</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Schedule</h2>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($schedule = mysqli_fetch_assoc($schedule_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['day']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if (isset($_GET['class_id'])): ?>
                <?php
                $class_id = $_GET['class_id'];
                $students_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM students WHERE class = ?";
                $stmt = mysqli_prepare($conn, $students_query);
                mysqli_stmt_bind_param($stmt, "i", $class_id);
                mysqli_stmt_execute($stmt);
                $students_result = mysqli_stmt_get_result($stmt);
                ?>
                <h2>Students in Class</h2>
                <form method="POST">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    <ul>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name']); ?>
                                </label>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <button type="submit" name="attendance">Mark Attendance</button>
                </form>
            <?php endif; ?>

        </div>
        <div id="Assignments" class="tabcontent">
            <h2>Create Assignment</h2>
            <form method="POST">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea><br>
                <label for="due_date">Due Date:</label>
                <input type="date" id="due_date" name="due_date" required><br>
                <label for="class_id">Class ID:</label>
                <input type="text" id="class_id" name="class_id" required><br>
                <button type="submit" name="assignment">Create Assignment</button>
            </form>
        </div>
    </div>
</body>
</html>
