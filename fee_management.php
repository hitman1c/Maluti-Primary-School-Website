<?php
session_start();
require_once "config.php";
include 'auth.php';
checkRole('admin');

require_once('fpdf/fpdf.php'); // Use FPDF library directly, ensure fpdf folder is present

if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

function backupFeePayment($conn, $student_id, $amount_due, $amount, $payment_date) {
    $sql = "INSERT INTO fees_backup (student_id, amount_due, amount, payment_date, backup_date) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "idds", $student_id, $amount_due, $amount, $payment_date);
    mysqli_stmt_execute($stmt);
}

function generateInvoicePDF($student_id, $conn) {
    // Fetch student and fee details
    $student_res = mysqli_query($conn, "SELECT s.first_name, s.last_name, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = $student_id");
    $student = mysqli_fetch_assoc($student_res);

    $fee_res = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = $student_id ORDER BY payment_date DESC LIMIT 1");
    $fee = mysqli_fetch_assoc($fee_res);

    if(!$student || !$fee) {
        return false;
    }

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Invoice',0,1,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,'Student: ' . $student['first_name'] . ' ' . $student['last_name'],0,1);
    $pdf->Cell(0,10,'Amount Due: $' . number_format($fee['amount_due'], 2),0,1);
    $pdf->Cell(0,10,'Amount Paid: $' . number_format($fee['amount'], 2),0,1);
    $pdf->Cell(0,10,'Payment Date: ' . $fee['payment_date'],0,1);
    $filename = "invoices/invoice_{$student_id}_" . date('YmdHis') . ".pdf";
    if(!is_dir('invoices')) {
        mkdir('invoices', 0777, true);
    }
    $pdf->Output('F', $filename);
    return $filename;
}

function sendInvoiceEmail($to_email, $to_name, $pdf_path) {
    $subject = "Invoice from School Management System";
    $message = "Dear $to_name,\n\nPlease find attached the invoice for your child.\n\nRegards,\nSchool Management";
    $headers = "From: no-reply@school.com";

    $file = $pdf_path;
    $filename = basename($file);
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));

    $separator = md5(time());

    $eol = PHP_EOL;

    $headers .= $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol;
    $body .= $message . $eol;

    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol;
    $body .= $content . $eol;
    $body .= "--" . $separator . "--";

    return mail($to_email, $subject, $body, $headers);
}

if(isset($_POST['record_payment'])) {
    $student_id = $_POST['student_id'];
    $amount_due = isset($_POST['amount_due']) ? $_POST['amount_due'] : 0;
    $amount = $_POST['amount'];
    $payment_date = date('Y-m-d');
    
    // Backup before insert
    backupFeePayment($conn, $student_id, $amount_due, $amount, $payment_date);

    $sql = "INSERT INTO fees (student_id, amount_due, amount, payment_date, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "idds", $student_id, $amount_due, $amount, $payment_date);
    mysqli_stmt_execute($stmt);

    // Generate invoice PDF
    $pdf_path = generateInvoicePDF($student_id, $conn);

    // Send invoice email to parent
    if($pdf_path) {
        $student_res = mysqli_query($conn, "SELECT first_name, last_name, email FROM students WHERE id = $student_id");
        $student = mysqli_fetch_assoc($student_res);
        if($student && !empty($student['email'])) {
            sendInvoiceEmail($student['email'], $student['first_name'] . ' ' . $student['last_name'], $pdf_path);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fee Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Fee Management</h2>
        <form method="post">
            <select name="student_id" required>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM students");
                while($row = mysqli_fetch_array($result)) {
                    echo "<option value='" . $row['id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</option>";
                }
                ?>
            </select>
            <input type="number" step="0.01" name="amount_due" placeholder="Amount Due" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount Paid" required>
            <button type="submit" name="record_payment">Record Payment</button>
        </form>
        
        <h3>Payment Records</h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Amount Due</th>
                <th>Amount Paid</th>
                <th>Date</th>
            </tr>
            <?php
            $sql = "SELECT students.first_name, students.last_name, fees.amount_due, fees.amount, fees.payment_date 
                    FROM fees 
                    JOIN students ON fees.student_id = students.id";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
                echo "<td>$" . ($row['amount_due'] ?? '0.00') . "</td>";
                echo "<td>$" . $row['amount'] . "</td>";
                echo "<td>" . $row['payment_date'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
