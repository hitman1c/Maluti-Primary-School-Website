<?php
session_start();
require_once "config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin") {
    header("location: login.php");
    exit;
}

if(isset($_POST['schedule'])) {
    $teacher_id = $_POST['teacher_id'];
    $class_name = $_POST['class_name'];
    $schedule_time = $_POST['schedule_time'];
    
    $sql = "INSERT INTO classes (teacher_id, name, schedule) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $teacher_id, $class_name, $schedule_time);
    mysqli_stmt_execute($stmt);
}

$teachers = mysqli_query($conn, "SELECT id, first_name, last_name FROM teachers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Teachers</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="main-content">
            <div class="card">
                <h2><i class="fas fa-calendar"></i> Schedule Teacher</h2>
                <form method="post" class="schedule-form">
                    <select name="teacher_id" required>
                        <option value="">Select Teacher</option>
                        <?php while($teacher = mysqli_fetch_assoc($teachers)): ?>
                            <option value="<?php echo $teacher['id']; ?>">
                                <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="class_name" placeholder="Class Name" required>
                    <input type="datetime-local" name="schedule_time" required>
                    <button type="submit" name="schedule" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Schedule Class
                    </button>
                </form>
            </div>

            <div class="card">
                <h2><i class="fas fa-list"></i> Current Schedules</h2>
                <table>
                    <tr>
                        <th>Teacher</th>
                        <th>Class</th>
                        <th>Schedule</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $schedules = mysqli_query($conn, "SELECT c.*, t.first_name, t.last_name 
                                                    FROM classes c 
                                                    JOIN teachers t ON c.teacher_id = t.id");
                    while($schedule = mysqli_fetch_assoc($schedules)):
                    ?>
                    <tr>
                        <td><?php echo $schedule['first_name'] . ' ' . $schedule['last_name']; ?></td>
                        <td><?php echo $schedule['name']; ?></td>
                        <td><?php echo $schedule['schedule']; ?></td>
                        <td>
                            <a href="edit_schedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_schedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
