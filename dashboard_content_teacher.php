<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Debugging: Check session data
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red; text-align: center;'>Error: User session is not set. Please log in again.</p>";
    exit;
}

// Fetch teacher details
$teacher_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, subject FROM teachers WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $teacher_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$teacher_result = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($teacher_result);

// Handle case where no teacher data is found or profile is incomplete
if (!$teacher) {
    echo "<p style='color: red; text-align: center;'>Error: No teacher profile found for the logged-in user. Please contact the administrator.</p>";
    exit;
}

if (empty($teacher['name']) || empty($teacher['subject'])) {
    echo "<p style='color: red; text-align: center;'>Error: Your profile is incomplete. Please update your profile.</p>";
    exit;
}

$teacher_id = $teacher['id'];

// Fetch counts for statistics
$class_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM classes WHERE teacher_id = {$teacher_id}"))['count'];
$student_count_query = "SELECT COUNT(*) as count FROM students WHERE class IN (SELECT name FROM classes WHERE teacher_id = {$teacher_id})";
$student_count_result = mysqli_query($conn, $student_count_query);
$student_count_row = mysqli_fetch_assoc($student_count_result);
$student_count = $student_count_row ? $student_count_row['count'] : 0;

$assignment_count_query = "SELECT COUNT(*) as count FROM assignments WHERE teacher_id = {$teacher_id}";
$assignment_count_result = mysqli_query($conn, $assignment_count_query);
$assignment_count_row = mysqli_fetch_assoc($assignment_count_result);
$assignment_count = $assignment_count_row ? $assignment_count_row['count'] : 0;


// Fetch submitted assignments (for dashboard view)
$submitted_assignments = [];
if (!isset($_GET['view']) || $_GET['view'] !== 'manage_classes') {
    $submitted_assignments_query = "SELECT submissions.id AS submission_id, assignments.title AS assignment_title,
                                          CONCAT(students.first_name, ' ', students.last_name) AS student_name,
                                          submissions.submission_date
                                          FROM submissions
                                          JOIN assignments ON submissions.assignment_id = assignments.id
                                          JOIN students ON submissions.student_id = students.id
                                          WHERE assignments.teacher_id = {$teacher_id}";
    $submitted_assignments_result = mysqli_query($conn, $submitted_assignments_query);
    if ($submitted_assignments_result) {
        while ($row = mysqli_fetch_assoc($submitted_assignments_result)) {
            $submitted_assignments[] = $row;
        }
    }
}


// Handle grade assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_grade'])) {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    // Fetch student_id and assignment_id from the submission
    $submission_info_query = "SELECT student_id, assignment_id FROM submissions WHERE id = ?";
    $stmt_info = mysqli_prepare($conn, $submission_info_query);
    mysqli_stmt_bind_param($stmt_info, "i", $submission_id);
    mysqli_stmt_execute($stmt_info);
    $submission_info_result = mysqli_stmt_get_result($stmt_info);
    $submission_info = mysqli_fetch_assoc($submission_info_result);

    if ($submission_info) {
        $student_id = $submission_info['student_id'];
        $assignment_id = $submission_info['assignment_id'];

        // Check if a grade already exists for this student and assignment
        $existing_grade_query = "SELECT id FROM grades WHERE student_id = ? AND assignment_id = ?";
        $stmt_check = mysqli_prepare($conn, $existing_grade_query);
        mysqli_stmt_bind_param($stmt_check, "ii", $student_id, $assignment_id);
        mysqli_stmt_execute($stmt_check);
        $existing_grade_result = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($existing_grade_result) > 0) {
            // Update existing grade
            $grade_query = "UPDATE grades SET grade = ? WHERE student_id = ? AND assignment_id = ?";
            $stmt_grade = mysqli_prepare($conn, $grade_query);
            mysqli_stmt_bind_param($stmt_grade, "sii", $grade, $student_id, $assignment_id);
            mysqli_stmt_execute($stmt_grade);
        } else {
            // Insert new grade
            $grade_query = "INSERT INTO grades (student_id, assignment_id, grade) VALUES (?, ?, ?)";
            $stmt_grade = mysqli_prepare($conn, $grade_query);
            mysqli_stmt_bind_param($stmt_grade, "iis", $student_id, $assignment_id, $grade);
            mysqli_stmt_execute($stmt_grade);
        }
    }

    // Redirect back to the dashboard or manage classes page
    $redirect_url = isset($_GET['view']) && $_GET['view'] === 'manage_classes' ? 'dashboard.php?view=manage_classes' : 'dashboard.php';
    header("Location: " . $redirect_url);
    exit;
}

// --- Manage Classes Logic ---
$teacher_classes = [];
$class_students = []; // To store students per class

if (isset($_GET['view']) && $_GET['view'] === 'manage_classes') {
    // Fetch teacher's classes
    $classes_query = "SELECT id, name, schedule, room_number FROM classes WHERE teacher_id = ?";
    $stmt = mysqli_prepare($conn, $classes_query);
    mysqli_stmt_bind_param($stmt, "i", $teacher_id);
    mysqli_stmt_execute($stmt);
    $classes_result = mysqli_stmt_get_result($stmt);

    if ($classes_result) {
        while ($class = mysqli_fetch_assoc($classes_result)) {
            $teacher_classes[] = $class;

            // Fetch students for this class
            // Simulate Sesotho-related student names
            $simulated_students = [
                ['first_name' => 'Thabo', 'last_name' => 'Mokoena'],
                ['first_name' => 'Lerato', 'last_name' => 'Mohapi'],
                ['first_name' => 'Mpho', 'last_name' => 'Moloi'],
                ['first_name' => 'Nthabiseng', 'last_name' => 'Kekana'],
                ['first_name' => 'Katleho', 'last_name' => 'Mabena'],
                ['first_name' => 'Puleng', 'last_name' => 'Tshabalala'],
                ['first_name' => 'Tshepo', 'last_name' => 'Khuzwayo'],
                ['first_name' => 'Palesa', 'last_name' => 'Ndlovu'],
                ['first_name' => 'Sipho', 'last_name' => 'Dlamini'],
                ['first_name' => 'Nokuthula', 'last_name' => 'Sibiya'],
                ['first_name' => 'Lebogang', 'last_name' => 'Mkhize'],
                ['first_name' => 'Kagiso', 'last_name' => 'Zulu'],
                ['first_name' => 'Boitumelo', 'last_name' => 'Molefe'],
                ['first_name' => 'Olebogeng', 'last_name' => 'Morake'],
                ['first_name' => 'Refilwe', 'last_name' => 'Mogale'],
                ['first_name' => 'Neo', 'last_name' => 'Ramaphosa'],
                ['first_name' => 'Karabo', 'last_name' => 'Nkosi'],
                ['first_name' => 'Itumeleng', 'last_name' => 'Motaung'],
                ['first_name' => 'Tebogo', 'last_name' => 'Mashaba'],
                ['first_name' => 'Dipuo', 'last_name' => 'Maluleke']
            ];

            // Assign a subset of simulated students to the class
            $class_students[$class['id']] = array_slice($simulated_students, 0, rand(5, count($simulated_students)));
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Error: Unable to fetch classes. Please try again later.</p>";
        exit;
    }
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
            position: fixed; /* Fixed sidebar */
            top: 0;
            left: 0;
            overflow-y: auto; /* Add scroll if content exceeds height */
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
            margin-left: 250px; /* Add margin to main content to avoid being hidden by fixed sidebar */
        }

        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 20px; /* Add margin below header */
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
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
            border-bottom: 2px solid #eee; /* Separator for card title */
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .list {
            max-height: 300px; /* Increased height for lists */
            overflow-y: auto;
            list-style: none; /* Remove default list styling */
            padding: 0;
        }

        .list li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list li form {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .list li input[type="text"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 80px; /* Adjust width as needed */
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            border: none; /* Remove button default border */
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Responsive grid */
            gap: 20px;
            margin-top: 10px;
        }

        .stat-item {
            background: #1cc88a; /* Default color, overridden below */
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-item h4 {
            margin: 0 0 10px 0;
            font-size: 1em;
        }

        .stat-item p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .stats-grid .stat-item:nth-child(1) { background: #1cc88a; } /* Classes */
        .stats-grid .stat-item:nth-child(2) { background: #36b9cc; } /* Students */
        .stats-grid .stat-item:nth-child(3) { background: #f6c23e; } /* Assignments */

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Styles for Manage Classes View */
        .manage-classes-section {
            margin-top: 20px;
        }

        .manage-classes-section h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .class-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .class-item h4 {
            margin-top: 0;
            font-size: 1.3em;
            color: #007bff;
            margin-bottom: 10px;
        }

        .class-details {
            margin-bottom: 15px;
            color: #555;
        }

        .class-details p {
            margin: 5px 0;
        }

        .students-list {
            list-style: none;
            padding: 0;
        }

        .students-list li {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            margin-bottom: 5px;
            padding: 8px;
            border-radius: 4px;
        }

        .add-class-form, .edit-class-form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .add-class-form h4, .edit-class-form h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }

        .add-class-form label, .edit-class-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .add-class-form input[type="text"], .edit-class-form input[type="text"] {
            width: calc(100% - 22px); /* Adjust for padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-class-form button, .edit-class-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-class-form button:hover, .edit-class-form button:hover {
            background-color: #218838;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section">
                <i class="fas fa-user-circle profile-icon"></i>
                <h3><?php echo htmlspecialchars($teacher['name']); ?></h3>
                <p>Teacher</p>
                <p>Subject: <?php echo htmlspecialchars($teacher['subject']); ?></p>
            </div>
            <div class="nav-items">
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
                <a href="dashboard.php?view=manage_classes" class="nav-item"><i class="fas fa-school"></i> Manage Classes</a>
                <a href="assignments.php" class="nav-item"><i class="fas fa-tasks"></i> Assignments</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="dashboard-header">Teacher Dashboard</div>

            <?php if (!isset($_GET['view']) || $_GET['view'] !== 'manage_classes'): ?>
                <!-- Dashboard View -->
                <div class="dashboard-cards">
                    <div class="card">
                        <h3>Statistics</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <h4>Classes</h4>
                                <p><?php echo $class_count; ?></p>
                            </div>
                            <div class="stat-item">
                                <h4>Students</h4>
                                <p><?php echo $student_count; ?></p>
                            </div>
                            <div class="stat-item">
                                <h4>Assignments</h4>
                                <p><?php echo $assignment_count; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Submitted Assignments</h3>
                        <div class="list">
                            <ul>
                                <?php if (empty($submitted_assignments)): ?>
                                    <li>No submitted assignments yet.</li>
                                <?php else: ?>
                                    <?php foreach ($submitted_assignments as $submission): ?>
                                        <li>
                                            <?php echo htmlspecialchars($submission['assignment_title'] . ' - ' . $submission['student_name'] . ' - Submitted on: ' . $submission['submission_date']); ?>
                                            <form method="POST" style="margin-top: 10px;">
                                                <input type="hidden" name="submission_id" value="<?php echo $submission['submission_id']; ?>">
                                                <input type="text" name="grade" placeholder="Enter Grade" required>
                                                <button type="submit" name="assign_grade" class="btn">Assign Grade</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card" id="class-schedule-card">
                        <h3>My Class Schedules</h3>
                        <div id="class-schedule-list">
                            <p>Loading classes...</p>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Quick Actions</h3>
                        <div class="action-buttons">
                            <a href="dashboard.php?view=manage_classes" class="btn"><i class="fas fa-school"></i> Manage Classes</a>
                            <a href="assignments.php" class="btn"><i class="fas fa-tasks"></i> Manage Assignments</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Manage Classes View -->
                <div class="manage-classes-section">
                    <h2><i class="fas fa-school"></i> Manage Classes</h2>

                    <!-- Placeholder for Add Class Form -->
                    <div class="add-class-form">
                        <h4>Add New Class (Placeholder)</h4>
                        <form action="#" method="POST">
                            <label for="class_name">Class Name:</label>
                            <input type="text" id="class_name" name="class_name" placeholder="e.g., Grade 10 Sesotho" required disabled>

                            <label for="schedule">Schedule:</label>
                            <input type="text" id="schedule" name="schedule" placeholder="e.g., Mon, Wed 10:00-11:00" required disabled>

                            <label for="room_number">Room Number:</label>
                            <input type="text" id="room_number" name="room_number" placeholder="e.g., Room 201" required disabled>

                            <button type="submit" disabled>Add Class</button>
                        </form>
                        <p><small>Adding class functionality is a placeholder.</small></p>
                    </div>

                    <h3>My Classes</h3>
                    <?php if (empty($teacher_classes)): ?>
                        <p>You are not assigned to any classes yet.</p>
                    <?php else: ?>
                        <?php foreach ($teacher_classes as $class): ?>
                            <div class="class-item">
                                <h4><?php echo htmlspecialchars($class['name']); ?></h4>
                                <div class="class-details">
                                    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($class['schedule']); ?></p>
                                    <p><strong>Room:</strong> <?php echo htmlspecialchars($class['room_number']); ?></p>
                                </div>
                                <h5>Students in this Class:</h5>
                                <?php if (isset($class_students[$class['id']]) && !empty($class_students[$class['id']])): ?>
                                    <ul class="students-list">
                                        <?php foreach ($class_students[$class['id']] as $student): ?>
                                            <li><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No students assigned to this class yet.</p>
                                <?php endif; ?>
                                <!-- Placeholder for Edit/Delete buttons -->
                                <div class="action-buttons" style="margin-top: 15px;">
                                    <button class="btn" disabled style="background-color: #ffc107;"><i class="fas fa-edit"></i> Edit (Placeholder)</button>
                                    <button class="btn" disabled style="background-color: #dc3545;"><i class="fas fa-trash-alt"></i> Delete (Placeholder)</button>
                                </div>
                                <p><small>Edit/Delete functionality is a placeholder.</small></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Only fetch classes for the dashboard view
            if (!window.location.search.includes('view=manage_classes')) {
                fetchClasses();
            }
        });

        function fetchClasses() {
            // This function is now simplified as the 'Manage Classes' view fetches all details
            // For the dashboard card, we can still fetch just the list if needed,
            // but for simplicity, we'll just show a placeholder or link.
             document.getElementById('class-schedule-list').innerHTML = '<p>View full schedules in <a href="dashboard.php?view=manage_classes">Manage Classes</a>.</p>';
        }
    </script>
</body>
</html>