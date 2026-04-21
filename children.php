<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php');
    exit;
}

// Fetch all students
$students_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, class FROM students";
$students_result = mysqli_query($conn, $students_query);

// Handle form submission to select a child
$selected_child_id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_child'])) {
    $selected_child_id = $_POST['child_id'];
}

// Generate random grades for subjects
function generateRandomGrades() {
    $subjects = ['English', 'Mathematics', 'Sesotho'];
    $grades = ['A', 'B', 'C', 'D', 'E'];
    $result = [];
    foreach ($subjects as $subject) {
        $result[$subject] = $grades[array_rand($grades)];
    }
    return $result;
}

// Generate random assignments
function generateAssignments($count = 10) {
    $assignments = [];
    for ($i = 1; $i <= $count; $i++) {
        $assignments[] = [
            'title' => "Assignment $i",
            'status' => rand(0, 1) ? 'Attended' : 'Unattended', // Randomly assign status
        ];
    }
    return $assignments;
}

$child_grades = $selected_child_id ? generateRandomGrades() : [];
$assignments = $selected_child_id ? generateAssignments() : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Children</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        select {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .assignments {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .assignment-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33.333% - 40px);
            min-width: 300px;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .assignment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .assignment-card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
        }

        .assignment-card p {
            margin: 10px 0;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .status.attended {
            background-color: #d4edda;
            color: #155724;
        }

        .status.unattended {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Children</h2>
        <form method="POST">
            <label for="child_id">Select Your Child:</label>
            <select name="child_id" id="child_id" required>
                <option value="">Select a child</option>
                <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo $selected_child_id == $student['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['name'] . " (Class: " . $student['class'] . ")"); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="select_child">View Details</button>
        </form>

        <?php if ($selected_child_id): ?>
            <h3>Grades</h3>
            <ul>
                <?php foreach ($child_grades as $subject => $grade): ?>
                    <li><?php echo htmlspecialchars($subject . ": " . $grade); ?></li>
                <?php endforeach; ?>
            </ul>

            <h3>Assignments</h3>
            <div class="assignments">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="assignment-card">
                        <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                        <p>Status: <span class="status <?php echo strtolower($assignment['status']); ?>">
                            <?php echo htmlspecialchars($assignment['status']); ?>
                        </span></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
