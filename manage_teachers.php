<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
include 'auth.php';
checkRole('admin');

if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch all teachers
$teachers_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, subject FROM teachers";
$teachers_result = mysqli_query($conn, $teachers_query);

// Fetch all classes
$classes_query = "SELECT id, name FROM classes";
$classes_result = mysqli_query($conn, $classes_query);

// Handle form submission for assigning classes and schedules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_schedule'])) {
    $teacher_id = $_POST['teacher_id'];
    $class_id = $_POST['class_id'];
    $schedule_date = $_POST['schedule_date'];
    $schedule_time = $_POST['schedule_time'];
    $event_type = $_POST['event_type'];

    // Ensure the teacher is not assigned more than two classes per day
    $check_query = "SELECT COUNT(*) AS class_count FROM schedules WHERE teacher_id = ? AND schedule_date = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "is", $teacher_id, $schedule_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $class_count = mysqli_fetch_assoc($result)['class_count'];

    if ($class_count >= 2 && $event_type === 'Class') {
        $error = "Error: A teacher cannot be assigned more than two classes per day.";
    } else {
        $insert_query = "INSERT INTO schedules (teacher_id, class_id, schedule_date, schedule_time, event_type) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iisss", $teacher_id, $class_id, $schedule_date, $schedule_time, $event_type);
        mysqli_stmt_execute($stmt);

        $success = "Schedule assigned successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editTeacher(id, first_name, last_name, subject, class_id) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_first_name').value = first_name;
            document.getElementById('edit_last_name').value = last_name;
            document.getElementById('edit_subject').value = subject;
            document.getElementById('edit_class').value = class_id;
            document.getElementById('editForm').style.display = 'block';
            window.scrollTo(0,0);
        }
        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Manage Teachers</h2>
        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <select name="class" required>
                <option value="">Select Class</option>
                <?php while($class = mysqli_fetch_assoc($classes_result)): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="add_teacher">Add Teacher</button>
        </form>
        <table>
            <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($teachers_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                <td>
                    <button onclick="editTeacher('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($row['name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['subject'])); ?>', '<?php echo $row['class_id'] ?? ''; ?>')">Edit</button> |
                    <a href="manage_teachers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this teacher?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <div id="editForm" style="display:none; margin-top:20px;">
            <h3>Edit Teacher</h3>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="first_name" id="edit_first_name" placeholder="First Name" required>
                <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name" required>
                <input type="text" name="subject" id="edit_subject" placeholder="Subject" required>
                <select name="class" id="edit_class" required>
                    <option value="">Select Class</option>
                    <?php
                    // Reset pointer and fetch classes again for edit form
                    $classes_result = mysqli_query($conn, "SELECT id, name FROM classes ORDER BY name ASC");
                    while($class = mysqli_fetch_assoc($classes_result)): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="update_teacher">Update Teacher</button>
                <button type="button" onclick="closeEditForm()">Cancel</button>
            </form>
        </div>

        <h3>Assign Classes and Schedules</h3>
        <form method="post">
            <select name="teacher_id" required>
                <option value="">Select Teacher</option>
                <?php
                mysqli_data_seek($teachers_result, 0); // Reset the result pointer
                while ($teacher = mysqli_fetch_assoc($teachers_result)): ?>
                    <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['name'] . " - " . $teacher['subject']); ?></option>
                <?php endwhile; ?>
            </select>
            <select name="class_id" required>
                <option value="">Select Class</option>
                <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="schedule_date" required>
            <input type="time" name="schedule_time" required>
            <select name="event_type" required>
                <option value="Class">Class</option>
                <option value="Meeting">Meeting</option>
                <option value="Sports Day">Sports Day</option>
                <option value="Lunch Week">Lunch Week</option>
            </select>
            <button type="submit" name="assign_schedule">Assign Schedule</button>
        </form>

        <h3>Existing Schedules</h3>
        <table>
            <tr>
                <th>Teacher</th>
                <th>Class</th>
                <th>Date</th>
                <th>Time</th>
                <th>Event Type</th>
            </tr>
            <?php
            $schedules_query = "SELECT teachers.first_name, teachers.last_name, classes.name AS class_name, schedule_date, schedule_time, event_type 
                                FROM schedules 
                                JOIN teachers ON schedules.teacher_id = teachers.id 
                                JOIN classes ON schedules.class_id = classes.id";
            $schedules_result = mysqli_query($conn, $schedules_query);
            while ($schedule = mysqli_fetch_assoc($schedules_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($schedule['first_name'] . " " . $schedule['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['schedule_date']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['schedule_time']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['event_type']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
