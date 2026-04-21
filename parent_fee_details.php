<?php
include 'auth.php';
include 'db_connection.php';
checkRoleWithCode('parent'); // Restrict access to parent users

$parent_user_id = $userData['id'] ?? $_SESSION['user_id'] ?? null;
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

if (!$student_id) {
    die("Invalid student ID.");
}

// Verify that the student belongs to the logged-in parent
$parent_res = mysqli_query($conn, "SELECT id FROM parents WHERE user_id = $parent_user_id LIMIT 1");
$parent = mysqli_fetch_assoc($parent_res);
$parent_id = $parent['id'] ?? null;

if (!$parent_id) {
    die("Parent record not found.");
}

$check_link = mysqli_query($conn, "SELECT * FROM parent_student WHERE parent_id = $parent_id AND student_id = $student_id");
if (mysqli_num_rows($check_link) === 0) {
    die("You do not have permission to view this student's fee details.");
}

// Fetch fee payment records for the student
$fees_res = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = $student_id ORDER BY payment_date DESC");
$fees = [];
while ($row = mysqli_fetch_assoc($fees_res)) {
    $fees[] = $row;
}

// Fetch student info
$student_res = mysqli_query($conn, "SELECT first_name, last_name FROM students WHERE id = $student_id");
$student = mysqli_fetch_assoc($student_res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fee Details for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Fee Details for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
        <?php if (count($fees) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $fee): ?>
                <tr>
                    <td><?php echo htmlspecialchars(number_format($fee['amount_due'], 2)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($fee['amount'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($fee['payment_date']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($fee['status'])); ?></td>
                    <td>
                        <?php
                        $invoice_path = "invoices/invoice_{$student_id}_{$fee['payment_date']}.pdf";
                        if (file_exists($invoice_path)) {
                            echo '<a href="' . $invoice_path . '" target="_blank">Download</a>';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No fee records found.</p>
        <?php endif; ?>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
