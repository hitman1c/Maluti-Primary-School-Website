<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Fetch available classes
$classes_query = "SELECT id, name FROM classes";
$classes_result = mysqli_query($conn, $classes_query);

// Fetch assignment statistics
$total_assignments_query = "SELECT COUNT(*) AS total FROM assignments";
$total_assignments_result = mysqli_query($conn, $total_assignments_query);
$total_assignments = mysqli_fetch_assoc($total_assignments_result)['total'];

$reviewed_assignments_query = "SELECT COUNT(*) AS reviewed FROM assignments WHERE reviewed = 1";
$reviewed_assignments_result = mysqli_query($conn, $reviewed_assignments_query);
$reviewed_assignments = mysqli_fetch_assoc($reviewed_assignments_result)['reviewed'];

$not_reviewed_assignments = $total_assignments - $reviewed_assignments;

// Fetch students with Sesotho names
$students_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM students WHERE language = 'Sesotho'";
$students_result = mysqli_query($conn, $students_query);


// Fetch all classes for the dropdown
$all_classes_query = "SELECT id, name FROM classes";
$all_classes_result = mysqli_query($conn, $all_classes_query);

// Handle class selection for filtering assignments
$selected_class_id = isset($_POST['class_filter']) ? $_POST['class_filter'] : null;
if ($selected_class_id) {
    $assignments_query = "SELECT assignments.id, assignments.title, assignments.description, assignments.due_date, classes.name AS class_name 
                          FROM assignments 
                          JOIN classes ON assignments.class_id = classes.id 
                          WHERE classes.id = ?";
    $stmt = mysqli_prepare($conn, $assignments_query);
    mysqli_stmt_bind_param($stmt, "i", $selected_class_id);
    mysqli_stmt_execute($stmt);
    $assignments_result = mysqli_stmt_get_result($stmt);
} else {
    $assignments_query = "SELECT assignments.id, assignments.title, assignments.description, assignments.due_date, classes.name AS class_name 
                          FROM assignments 
                          JOIN classes ON assignments.class_id = classes.id";
    $assignments_result = mysqli_query($conn, $assignments_query);
}

// Handle form submission for adding assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_assignment'])) {
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $insert_query = "INSERT INTO assignments (class_id, title, description, due_date, reviewed) VALUES (?, ?, ?, ?, 0)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "isss", $class_id, $title, $description, $due_date);
    mysqli_stmt_execute($stmt);

    echo "Assignment added successfully!";
}

// Handle form submission for assigning grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
    $student_id = $_POST['student_id'];
    $assignment_id = $_POST['assignment_id'];
    $grade = $_POST['grade'];

    $grade_query = "INSERT INTO grades (student_id, assignment_id, grade) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $grade_query);
    mysqli_stmt_bind_param($stmt, "iis", $student_id, $assignment_id, $grade);
    mysqli_stmt_execute($stmt);

    echo "Grade assigned successfully!";
}

// Handle form submission for marking assignments as reviewed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_reviewed'])) {
    $assignment_id = $_POST['assignment_id'];

    $update_query = "UPDATE assignments SET reviewed = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $assignment_id);
    mysqli_stmt_execute($stmt);

    echo "Assignment marked as reviewed!";
}

// Handle form submission for editing assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $update_query = "UPDATE assignments SET title = ?, description = ?, due_date = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $due_date, $assignment_id);
    mysqli_stmt_execute($stmt);

    echo "Assignment updated successfully!";
}

// Handle form submission for editing grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_grade'])) {
    $grade_id = $_POST['grade_id'];
    $grade = $_POST['grade'];

    $update_query = "UPDATE grades SET grade = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $grade, $grade_id);
    mysqli_stmt_execute($stmt);

    echo "Grade updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ...existing styles... */
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
                <h3><?php echo htmlspecialchars($_SESSION['username'] ?? 'Unknown'); ?></h3>
                <p>Teacher</p>
            </div>
            <div class="nav-items">
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_classes.php" class="nav-item"><i class="fas fa-school"></i> Manage Classes</a>
                <a href="assignments.php" class="nav-item"><i class="fas fa-tasks"></i> Assignments</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="dashboard-header">Teacher Dashboard</div>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Assignment Statistics</h3>
                    <p>Total Assignments: <?php echo $total_assignments; ?></p>
                    <p>Reviewed Assignments: <?php echo $reviewed_assignments; ?></p>
                    <p>Not Reviewed Assignments: <?php echo $not_reviewed_assignments; ?></p>
                </div>
                <div class="card">
                    <h3>Add New Assignment</h3>
                    <form method="post">
                        <select name="class_id" required>
                            <option value="">Select Class</option>
                            <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                                <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="title" placeholder="Assignment Title" required>
                        <textarea name="description" placeholder="Description"></textarea>
                        <input type="date" name="due_date" required>
                        <button type="submit" name="add_assignment">Add Assignment</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Filter Assignments by Class</h3>
                    <form method="post">
                        <select name="class_filter" required>
                            <option value="">Select Class</option>
                            <?php while ($class = mysqli_fetch_assoc($all_classes_result)): ?>
                                <option value="<?php echo $class['id']; ?>" <?php echo $selected_class_id == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit">Filter</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Existing Assignments</h3>
                    <table>
                        <tr>
                            <th>Class</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                        <input type="text" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
                                        <textarea name="description"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
                                        <input type="date" name="due_date" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required>
                                        <button type="submit" name="edit_assignment">Edit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
                <div class="card">
                    <h3>Assign Grades</h3>
                    <form method="post">
                        <select name="student_id" required>
                            <option value="">Select Student</option>
                            <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select name="assignment_id" required>
                            <option value="">Select Assignment</option>
                            <?php
                            mysqli_data_seek($assignments_result, 0); // Reset assignments result pointer
                            while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                                <option value="<?php echo $assignment['id']; ?>"><?php echo htmlspecialchars($assignment['title']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="grade" placeholder="Grade" required>
                        <button type="submit" name="assign_grade">Assign Grade</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Edit Grades</h3>
                    <table>
                        <tr>
                            <th>Student</th>
                            <th>Assignment</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        $grades_query = "SELECT grades.id AS grade_id, CONCAT(students.first_name, ' ', students.last_name) AS student_name, 
                                         assignments.title AS assignment_title, grades.grade 
                                         FROM grades 
                                         JOIN students ON grades.student_id = students.id 
                                         JOIN assignments ON grades.assignment_id = assignments.id";
                        $grades_result = mysqli_query($conn, $grades_query);
                        while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="grade_id" value="<?php echo $grade['grade_id']; ?>">
                                        <input type="text" name="grade" value="<?php echo htmlspecialchars($grade['grade']); ?>" required>
                                        <button type="submit" name="edit_grade">Edit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
                <div class="card">
                    <h3>Mark Assignments as Reviewed</h3>
                    <form method="post">
                        <select name="assignment_id" required>
                            <option value="">Select Assignment</option>
                            <?php
                            $unreviewed_assignments_query = "SELECT id, title FROM assignments WHERE reviewed = 0";
                            $unreviewed_assignments_result = mysqli_query($conn, $unreviewed_assignments_query);
                            while ($assignment = mysqli_fetch_assoc($unreviewed_assignments_result)): ?>
                                <option value="<?php echo $assignment['id']; ?>"><?php echo htmlspecialchars($assignment['title']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" name="mark_reviewed">Mark as Reviewed</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
