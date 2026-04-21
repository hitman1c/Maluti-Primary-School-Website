<!DOCTYPE html>
<html>
<head>
    <title>Manage Assignments</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 60%;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        table, th, td {
            border: 1px solid #bbb;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Assignments</h2>

        <?php
        $file = "assignments.txt";

        // Save the form input
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_assignment'])) {
            $class = htmlspecialchars($_POST['class_id']);
            $title = htmlspecialchars($_POST['title']);
            $description = htmlspecialchars($_POST['description']);
            $due_date = htmlspecialchars($_POST['due_date']);

            $entry = "$class|$title|$description|$due_date\n";
            file_put_contents($file, $entry, FILE_APPEND);
        }
        ?>

        <form method="post">
            <select name="class_id" required>
                <option value="">Select Class</option>
                <option value="Maths">Maths</option>
                <option value="English">English</option>
                <option value="Science">Science</option>
                <option value="History">History</option>
                <option value="Geography">Geography</option>
                <option value="ICT">ICT</option>
                <option value="Life Skills">Life Skills</option>
                <option value="Business Studies">Business Studies</option>
            </select>
            <input type="text" name="title" placeholder="Assignment Title" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="date" name="due_date" required>
            <button type="submit" name="add_assignment">Add Assignment</button>
        </form>

        <h3>Existing Assignments</h3>
        <table>
            <tr>
                <th>Class</th>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
            </tr>
            <?php
            if (file_exists($file)) {
                $lines = file($file, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $line) {
                    list($class, $title, $description, $due_date) = explode("|", $line);
                    echo "<tr>
                            <td>$class</td>
                            <td>$title</td>
                            <td>$description</td>
                            <td>$due_date</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No assignments found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <footer class="footer">
        <p>&copy; 2025 School Management System. All rights reserved.</p>
        <p>Developed by Seabata</p>
    </footer>
</body>
</html>
