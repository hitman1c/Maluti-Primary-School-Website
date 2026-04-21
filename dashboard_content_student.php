<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

// Fetch student details
$student_query = "SELECT CONCAT(first_name, ' ', last_name) AS name, class FROM students WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $student_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$student_result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($student_result);

// Handle case where no student data is found
if (!$student) {
    $student = ['name' => 'Unknown', 'class' => 'Unknown'];
}

// Fetch assignments for the student's class
$assignments_query = "SELECT title, description, due_date FROM assignments WHERE class_id = ?";
$stmt = mysqli_prepare($conn, $assignments_query);
mysqli_stmt_bind_param($stmt, "s", $student['class']);
mysqli_stmt_execute($stmt);
$assignments_result = mysqli_stmt_get_result($stmt);

$random_grade = rand(50, 100); // Generate random grade between 50 and 100
$grades_result = null; // No DB grades, will use random grade in display
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            padding: 20px;
        }

        .sidebar .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .profile-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .sidebar .nav-items {
            list-style: none;
            padding: 0;
        }

        .sidebar .nav-item {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background 0.3s ease;
        }

        .sidebar .nav-item:hover {
            background-color: #0056b3;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.8em;
            font-weight: bold;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33.333% - 40px);
            min-width: 300px;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
        }

        .list {
            max-height: 200px;
            overflow-y: auto;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
                <h3><?php echo htmlspecialchars($student['name']); ?></h3>
                <p>Student</p>
            </div>
            <div class="nav-items">
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
                <a href="assignments.php" class="nav-item"><i class="fas fa-tasks"></i> Assignments</a>
                <a href="grades.php" class="nav-item"><i class="fas fa-graduation-cap"></i> Grades</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="dashboard-header">Student Dashboard</div>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Available Assignment</h3>
                    <div class="list">
                        <form id="assignmentForm">
                            <label for="answer" id="questionLabel"><strong>What is 7 x 5 = ?</strong></label><br>
                            <input type="number" id="answer" name="answer" required>
                            <button type="submit" class="btn">Submit Answer</button>
                        </form>
                        <p id="responseMessage" style="color: green; font-weight: bold; display:none;">Answer submitted successfully!</p>
                    </div>
                </div>
                <div class="card">
                    <h3>Grades</h3>
                    <div class="list">
                        <ul>
                            <li>Math Assignment: <?php echo $random_grade; ?>%</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="assignments.php" class="btn">View Assignments</a>
                        <a href="grades.php" class="btn">View Grades</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const questions = [
            { question: "What is 7 x 5 = ?", answer: 35 },
            { question: "What is 9 + 6 = ?", answer: 15 },
            { question: "What is 12 - 4 = ?", answer: 8 },
            { question: "What is 3 x 8 = ?", answer: 24 },
            { question: "What is 20 / 4 = ?", answer: 5 }
        ];
        let currentQuestionIndex = 0;

        const questionLabel = document.getElementById('questionLabel');
        const answerInput = document.getElementById('answer');
        const responseMessage = document.getElementById('responseMessage');
        const form = document.getElementById('assignmentForm');

        function loadQuestion(index) {
            questionLabel.innerHTML = "<strong>" + questions[index].question + "</strong>";
            answerInput.value = '';
            responseMessage.style.display = 'none';
        }

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const userAnswer = parseInt(answerInput.value);
            if (userAnswer === questions[currentQuestionIndex].answer) {
                responseMessage.style.display = 'block';
                currentQuestionIndex++;
                if (currentQuestionIndex < questions.length) {
                    setTimeout(() => {
                        loadQuestion(currentQuestionIndex);
                    }, 1000);
                } else {
                    questionLabel.innerHTML = "All questions answered successfully!";
                    answerInput.style.display = 'none';
                    form.querySelector('button').style.display = 'none';
                }
            } else {
                alert('Incorrect answer. Please try again.');
            }
        });

        loadQuestion(currentQuestionIndex);
    </script>
</body>
</html>
