<?php
include 'auth.php';
checkRole('admin');

require_once "config.php";

function generateReportForStudents($conn, $student_ids = []) {
    $whereClause = "";
    if (!empty($student_ids)) {
        $ids = implode(',', array_map('intval', $student_ids));
        $whereClause = "WHERE s.id IN ($ids)";
    }

    $sql = "SELECT s.id, s.first_name, s.last_name, c.name AS class_name, s.class AS class_id,
            SUM(f.amount) AS total_paid, SUM(f.amount_due) AS total_due
            FROM students s
            LEFT JOIN classes c ON s.class = c.id
            LEFT JOIN fees f ON s.id = f.student_id
            $whereClause
            GROUP BY s.id, s.first_name, s.last_name, c.name, s.class
            ORDER BY s.last_name, s.first_name";

    $result = mysqli_query($conn, $sql);

    $reportData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reportData[] = $row;
    }
    return $reportData;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_students = $_POST['student_ids'] ?? [];
    $reportData = generateReportForStudents($conn, $selected_students);
} else {
    $reportData = generateReportForStudents($conn);
}

// Fetch all students for selection
$students_result = mysqli_query($conn, "SELECT id, first_name, last_name FROM students ORDER BY last_name, first_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Generate Student Reports</h2>
        <form method="post">
            <label for="student_ids">Select Students (Ctrl+Click for multiple):</label><br>
            <select name="student_ids[]" id="student_ids" multiple size="10" style="width: 100%; border-radius: 6px; border: 1.5px solid #ddd; padding: 10px;">
                <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo (isset($selected_students) && in_array($student['id'], $selected_students)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" style="margin-top: 15px;">Generate Report</button>
        </form>

        <h3>Report Results</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Class</th>
                <th>Admission Date</th>
                <th>Total Paid</th>
                <th>Total Due</th>
            </tr>
            <?php foreach ($reportData as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
            <td><?php echo htmlspecialchars($row['admission_date'] ?? 'N/A'); ?></td>
                <td>$<?php echo number_format($row['total_paid'], 2); ?></td>
                <td>$<?php echo number_format($row['total_due'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
