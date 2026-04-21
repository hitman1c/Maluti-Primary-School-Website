<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
include 'auth.php'; // Include the authentication file
checkRole('admin'); // Restrict access to admin users

if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

// Fetch classes for dropdown
$classes_result = mysqli_query($conn, "SELECT id, name FROM classes ORDER BY name ASC");

// Handle add student
if(isset($_POST['add_student'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class = $_POST['class'];
    $admission_date = $_POST['admission_date'];

    // Ensure the admission_date column exists in the database
    $sql = "INSERT INTO students (first_name, last_name, class, admission_date) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $firstname, $lastname, $class, $admission_date);
    mysqli_stmt_execute($stmt);
    header("Location: manage_students.php");
    exit;
}

// Handle delete student
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Backup student data before deletion
    $backup_sql = "INSERT INTO students_backup (id, first_name, last_name, class, admission_date, backup_date)
                   SELECT id, first_name, last_name, class, admission_date, NOW() FROM students WHERE id = ?";
    $backup_stmt = mysqli_prepare($conn, $backup_sql);
    mysqli_stmt_bind_param($backup_stmt, "i", $id);
    mysqli_stmt_execute($backup_stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_students.php");
    exit;
}

// Handle update student
if(isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class = $_POST['class'];
    $admission_date = $_POST['admission_date'];

    $sql = "UPDATE students SET first_name = ?, last_name = ?, class = ?, admission_date = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $firstname, $lastname, $class, $admission_date, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_students.php");
    exit;
}

// Fetch students with class names
$students_query = "SELECT s.id, s.first_name, s.last_name, c.name AS class_name, s.class FROM students s LEFT JOIN classes c ON s.class = c.name ORDER BY s.last_name, s.first_name";
$students_result = mysqli_query($conn, $students_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editStudent(id, firstname, lastname, class_name, admission_date) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_firstname').value = firstname;
            document.getElementById('edit_lastname').value = lastname;
            document.getElementById('edit_class').value = class_name;
            document.getElementById('edit_admission_date').value = admission_date;
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
        <h2>Manage Students</h2>

        <h3>Add New Student</h3>
        <form method="post">
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="lastname" placeholder="Last Name" required>
            <select name="class" required>
                <option value="">Select Class</option>
                <?php while($class = mysqli_fetch_assoc($classes_result)): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <label for="admission_date">Admission Date:</label>
            <input type="date" name="admission_date" required>
            <button type="submit" name="add_student">Add Student</button>
        </form>

        <h3>Existing Students</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Class</th>
                <th>Admission Date</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($students_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                <td><?php echo htmlspecialchars($row['admission_date'] ?? ''); ?></td>
                <td>
                    <button onclick="editStudent('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($row['first_name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['last_name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['class_name'])); ?>', '<?php echo htmlspecialchars($row['admission_date'] ?? ''); ?>')">Edit</button>
                    <a href="manage_students.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <div id="editForm" style="display:none; margin-top:20px;">
            <h3>Edit Student</h3>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="firstname" id="edit_firstname" placeholder="First Name" required>
                <input type="text" name="lastname" id="edit_lastname" placeholder="Last Name" required>
                <select name="class" id="edit_class" required>
                    <option value="">Select Class</option>
                    <?php
                    // Reset pointer and fetch classes again for edit form
                    $classes_result = mysqli_query($conn, "SELECT id, name FROM classes ORDER BY name ASC");
                    while($class = mysqli_fetch_assoc($classes_result)): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="admission_date">Admission Date:</label>
                <input type="date" name="admission_date" id="edit_admission_date" required>
                <button type="submit" name="update_student">Update Student</button>
                <button type="button" onclick="closeEditForm()">Cancel</button>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
