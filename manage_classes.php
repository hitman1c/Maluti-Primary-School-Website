<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

$message = '';

// Handle add class
if(isset($_POST['add_class'])) {
    $name = trim($_POST['name']);
    $schedule = trim($_POST['schedule']);
    if($name !== '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO classes (name, schedule) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $schedule);
        if(mysqli_stmt_execute($stmt)) {
            $message = "Class added successfully.";
        } else {
            $message = "Error adding class.";
        }
    } else {
        $message = "Class name cannot be empty.";
    }
}

// Handle delete class
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM classes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if(mysqli_stmt_execute($stmt)) {
        $message = "Class deleted successfully.";
    } else {
        $message = "Error deleting class.";
    }
}

// Handle edit class
if(isset($_POST['edit_class'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $schedule = trim($_POST['schedule']);
    if($name !== '') {
        $stmt = mysqli_prepare($conn, "UPDATE classes SET name = ?, schedule = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $schedule, $id);
        if(mysqli_stmt_execute($stmt)) {
            $message = "Class updated successfully.";
        } else {
            $message = "Error updating class.";
        }
    } else {
        $message = "Class name cannot be empty.";
    }
}

// Fetch classes
$classes = [];
$result = mysqli_query($conn, "SELECT * FROM classes ORDER BY name ASC");
while($row = mysqli_fetch_assoc($result)) {
    $classes[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editClass(id, name, schedule) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_schedule').value = schedule;
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
        <h2>Manage Classes</h2>
        <?php if($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <h3>Add New Class</h3>
        <form method="post">
            <input type="text" name="name" placeholder="Class Name" required>
            <input type="datetime-local" name="schedule" placeholder="Schedule (optional)">
            <button type="submit" name="add_class">Add Class</button>
        </form>

        <h3>Existing Classes</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Schedule</th>
                <th>Actions</th>
            </tr>
            <?php foreach($classes as $class): ?>
            <tr>
                <td><?php echo htmlspecialchars($class['name']); ?></td>
                <td><?php echo htmlspecialchars($class['schedule']); ?></td>
                <td>
                    <button onclick="editClass('<?php echo $class['id']; ?>', '<?php echo htmlspecialchars(addslashes($class['name'])); ?>', '<?php echo htmlspecialchars($class['schedule']); ?>')">Edit</button>
                    <a href="?delete=<?php echo $class['id']; ?>" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div id="editForm" style="display:none; margin-top:20px;">
            <h3>Edit Class</h3>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="name" id="edit_name" placeholder="Class Name" required>
                <input type="datetime-local" name="schedule" id="edit_schedule" placeholder="Schedule (optional)">
                <button type="submit" name="edit_class">Update Class</button>
                <button type="button" onclick="closeEditForm()">Cancel</button>
            </form>
        </div>
    </div>
</body>
</html>
