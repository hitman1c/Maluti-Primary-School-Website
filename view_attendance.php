<?php
include 'auth.php';
include 'db_connection.php';

if ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'parent') {
    header('Location: unauthorized.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'student') {
    // Fetch attendance records for the student
    $student_query = "SELECT id, email FROM students WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $student_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    if (!$student) {
        die("Student record not found.");
    }
    $student_id = $student['id'];

    $attendance_query = "SELECT a.date, a.status, c.name AS class_name 
                         FROM attendance a 
                         JOIN classes c ON a.class_id = c.id 
                         WHERE a.student_id = ? 
                         ORDER BY a.date DESC";
    $stmt = mysqli_prepare($conn, $attendance_query);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $attendance_result = mysqli_stmt_get_result($stmt);

    // Fetch assignments for the student
    $assignments_query = "SELECT id, title, description, file_path FROM assignments WHERE class_id = (SELECT class FROM students WHERE id = ?)";
    $stmt = mysqli_prepare($conn, $assignments_query);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $assignments_result = mysqli_stmt_get_result($stmt);

} elseif ($role === 'parent') {
    // Fetch children of the parent
    $parent_query = "SELECT id, email FROM parents WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $parent_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $parent = mysqli_fetch_assoc($result);
    if (!$parent) {
        die("Parent record not found.");
    }
    $parent_id = $parent['id'];

    // Fetch students linked to the parent
    $children_query = "SELECT s.id, s.first_name, s.last_name FROM students s 
                       JOIN parent_student ps ON s.id = ps.student_id 
                       WHERE ps.parent_id = ?";
    $stmt = mysqli_prepare($conn, $children_query);
    mysqli_stmt_bind_param($stmt, "i", $parent_id);
    mysqli_stmt_execute($stmt);
    $children_result = mysqli_stmt_get_result($stmt);

    // Fetch attendance and grades for each child
    $attendance_records = [];
    while ($child = mysqli_fetch_assoc($children_result)) {
        $attendance_query = "SELECT a.date, a.status, c.name AS class_name 
                             FROM attendance a 
                             JOIN classes c ON a.class_id = c.id 
                             WHERE a.student_id = ? 
                             ORDER BY a.date DESC";
        $stmt = mysqli_prepare($conn, $attendance_query);
        mysqli_stmt_bind_param($stmt, "i", $child['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $attendance_records[$child['id']] = [
            'child' => $child,
            'attendance' => mysqli_fetch_all($result, MYSQLI_ASSOC)
        ];

        $grades_query = "SELECT subject, grade FROM grades WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $grades_query);
        mysqli_stmt_bind_param($stmt, "i", $child['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $attendance_records[$child['id']]['grades'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance and Grades</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Attendance and Grades</h2>
        <?php if ($role === 'student'): ?>
            <h3>Your Attendance</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($attendance_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Your Assignments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Download</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download>Download</a></td>
                            <td>
                                <form method="post" action="submit_feedback.php">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                    <textarea name="feedback" placeholder="Enter your feedback" required></textarea>
                                    <button type="submit">Submit</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($role === 'parent'): ?>
            <h3>Your Children's Attendance and Grades</h3>
            <?php foreach ($attendance_records as $record): ?>
                <h4><?php echo htmlspecialchars($record['child']['first_name'] . ' ' . $record['child']['last_name']); ?></h4>
                <h5>Attendance</h5>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($record['attendance'] as $att): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($att['date']); ?></td>
                                <td><?php echo htmlspecialchars($att['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($att['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h5>Grades</h5>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($record['grades'] as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
