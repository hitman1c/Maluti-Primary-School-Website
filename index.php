<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maluti Primary School</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:y@400;700&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            background: url('mypic.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            line-height: 1.6;
        }

        /* Header */
        header {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            color: #ffcc00;
        }

        header p {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        /* Navigation */
        nav {
            display: flex;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 10px 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ffcc00;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .hero .cta {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .hero .button {
            background-color: #ffcc00;
            color: black;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hero .button:hover {
            background-color: #e6b800;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            background-color: #ffcc00;
            color: black;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #e6b800;
        }

        .close {
            background: none;
            border: none;
            font-size: 20px;
            font-weight: bold;
            color: black;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        /* Notifications Section */
        .notifications {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
        }

        .notifications h3 {
            margin-bottom: 10px;
        }

        .notifications ul {
            list-style: none;
            padding: 0;
        }

        .notifications ul li {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .notifications ul li:last-child {
            border-bottom: none;
        }

        .notifications form {
            margin-top: 20px;
        }

        .notifications form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .notifications form button {
            background-color: #ffcc00;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .notifications form button:hover {
            background-color: #e6b800;
        }

        .reply-section {
            margin-top: 10px;
            padding-left: 20px;
        }

        .reply-section p {
            margin-bottom: 5px;
        }

        .reply-section form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .reply-section form button {
            background-color: #ffcc00;
            color: black;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .reply-section form button:hover {
            background-color: #e6b800;
        }

        /* Grades Section */
        .grades-section {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
        }

        .grades-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .grades-section table th, .grades-section table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .grades-section table th {
            background-color: #ffcc00;
            color: black;
        }

        /* Grades Summary Section */
        .grades-summary {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            max-width: 800px;
            color: white;
        }

        .grade-item {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.2rem;
            font-weight: bold;
            width: 120px;
        }

        .grade-a {
            background-color: #28a745; /* Deep Green */
            color: white;
        }

        .grade-b {
            background-color: #ffc107; /* Yellow */
            color: black;
        }

        .grade-c {
            background-color: #fd7e14; /* Light Orange */
            color: white;
        }

        .grade-d {
            background-color: #dc3545; /* Slightly Red */
            color: white;
        }

        .grade-e {
            background-color: #721c24; /* Red */
            color: white;
        }

        .grade-u {
            background-color: #6c757d; /* Gray */
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Maluti Primary School</h1>
        <p>Welcome to the official website of Maluti Primary School</p>
    </header>
    <nav>
        <a href="about.php">About Us</a>
        <a href="features.php">Features</a>
        <a href="#testimonials">Testimonials</a>
        <a href="#contact">Contact Us</a>
    </nav>
    <div class="hero">
        <h2>Welcome to Maluti Primary School</h2>
        <p>Empowering young minds through quality education and cultural values.</p>
        <div class="cta">
            <a href="login.php" class="button">Login</a>
            <a href="signup.php" class="button">Sign Up</a>
        </div>
    </div>

    <!-- Grades Summary Section -->
    <div class="grades-summary">
        <div class="grade-item grade-d">English: D</div>
        <div class="grade-item grade-d">Mathematics: D</div>
        <div class="grade-item grade-e">Sesotho: E</div>
    </div>

    <div class="notifications">
        <h3>Notifications</h3>
        <ul>
            <?php
            include 'db_connection.php';
            $notifications_query = "SELECT id, message, created_at FROM notifications ORDER BY created_at DESC LIMIT 10";
            $notifications_result = mysqli_query($conn, $notifications_query);

            while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                <li>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></small>
                    <div class="reply-section">
                        <h4>Replies:</h4>
                        <?php
                        // Check if the 'replies' table exists
                        $table_check_query = "SHOW TABLES LIKE 'replies'";
                        $table_check_result = mysqli_query($conn, $table_check_query);

                        if (mysqli_num_rows($table_check_result) === 0) {
                            echo "<p class='error'>Replies are currently unavailable. Please contact the administrator.</p>";
                        } else {
                            $replies_query = "SELECT message, created_at FROM replies WHERE notification_id = " . $notification['id'];
                            $stmt = mysqli_prepare($conn, $replies_query);
                            if ($stmt) {
                                mysqli_stmt_execute($stmt);
                                $replies_result = mysqli_stmt_get_result($stmt);
                                while ($reply = mysqli_fetch_assoc($replies_result)): ?>
                                    <p><?php echo htmlspecialchars($reply['message']); ?> <small>(<?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?>)</small></p>
                                <?php endwhile;
                            }
                        }
                        ?>
                        <form method="POST" action="send_reply.php">
                            <textarea name="reply_message" rows="2" placeholder="Write a reply..." required></textarea>
                            <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                            <button type="submit">Reply</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
        <form method="POST" action="send_notification.php">
            <textarea name="message" rows="4" placeholder="Write a reply or new notification..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
    <div class="grades-section">
        <h3>Grades</h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Assignment</th>
                <th>Grade</th>
                <th>Teacher</th>
            </tr>
            <?php
            // Function to determine grade class
            function getGradeClass($grade) {
                switch ($grade) {
                    case 'A':
                        return 'grade-a';
                    case 'B':
                        return 'grade-b';
                    case 'C':
                        return 'grade-c';
                    case 'D':
                        return 'grade-d';
                    case 'E':
                        return 'grade-e';
                    case 'U':
                        return 'grade-u';
                    default:
                        return '';
                }
            }

            $grades_query = "SELECT 
                                CONCAT(students.first_name, ' ', students.last_name) AS student_name, 
                                assignments.title AS assignment_title, 
                                grades.grade, 
                                CONCAT(teachers.first_name, ' ', teachers.last_name) AS teacher_name 
                             FROM grades 
                             JOIN assignments ON grades.assignment_id = assignments.id 
                             JOIN students ON grades.student_id = students.id 
                             JOIN teachers ON assignments.teacher_id = teachers.id 
                             ORDER BY students.id, assignments.title";
            $grades_result = mysqli_query($conn, $grades_query);
            while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['assignment_title']); ?></td>
                    <td class="<?php echo getGradeClass($grade['grade']); ?>">
                        <?php echo htmlspecialchars($grade['grade']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($grade['teacher_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <footer>
        <p>Maluti Primary School: Empowering young minds through quality education and cultural values. Contact us at +266 56171110 for more information about our programs, events, and admissions.</p>
    </footer>
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>


