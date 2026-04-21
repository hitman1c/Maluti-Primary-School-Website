<?php
session_start();
require_once "config.php";
require_once "auth_check.php";
checkAuth('teacher');

$teacher_id = $_SESSION['user_id'];

// Fetch assignments for this teacher
$assignments = [];
$sql = "SELECT id, title FROM assignments WHERE teacher_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $teacher_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($row = mysqli_fetch_assoc($result)) {
    $assignments[] = $row;
}

// Fetch students in classes taught by this teacher
$students = [];
$sql = "SELECT s.id, s.first_name, s.last_name, s.class FROM students s JOIN classes c ON s.class = c.name WHERE c.teacher_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $teacher_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

if(isset($_POST['submit_grades'])) {
    $assignment_id = $_POST['assignment_id'];
    foreach($_POST['grades'] as $student_id => $grade) {
        $grade_val = trim($grade);
        if($grade_val !== '') {
            // Insert or update grade
            $sql = "INSERT INTO grades (student_id, assignment_id, grade) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE grade = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $student_id, $assignment_id, $grade_val, $grade_val);
            mysqli_stmt_execute($stmt);
        }
    }
    $message = "Grades submitted successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grade Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Grade Management</h2>
        <?php if(isset($message)): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="assignment_id">Select Assignment:</label>
            <select name="assignment_id" id="assignment_id" required>
                <option value="">--Select Assignment--</option>
                <?php foreach($assignments as $assignment): ?>
                    <option value="<?php echo $assignment['id']; ?>"><?php echo htmlspecialchars($assignment['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <table>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Grade</th>
                </tr>
                <?php foreach($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                    <td><input type="text" name="grades[<?php echo $student['id']; ?>]" size="5"></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" name="submit_grades">Submit Grades</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
