<?php
session_start();
require_once "config.php";

if(!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

function getAttendanceRate($student_id) {
    global $conn;
    $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present 
            FROM attendance WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] > 0 ? ($row['present'] / $row['total']) * 100 : 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h2>Performance Reports</h2>
        <div class="report-section">
            <canvas id="performanceChart"></canvas>
            <?php
            $result = mysqli_query($conn, "SELECT s.first_name, s.last_name, AVG(g.grade) as avg_grade 
                                         FROM students s 
                                         LEFT JOIN grades g ON s.id = g.student_id 
                                         GROUP BY s.id");
            $names = [];
            $grades = [];
            while($row = mysqli_fetch_array($result)) {
                $names[] = $row['first_name'] . ' ' . $row['last_name'];
                $grades[] = $row['avg_grade'];
            }
            ?>
        </div>
        
        <script>
        new Chart(document.getElementById('performanceChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($names); ?>,
                datasets: [{
                    label: 'Average Grade',
                    data: <?php echo json_encode($grades); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });
        </script>
    </div>
</body>
</html>
