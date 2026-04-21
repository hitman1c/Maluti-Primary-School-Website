<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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
            background-color: #111; /* Dark background */
            color: #fff; /* Text color */
        }

        /* Header */
        header {
            background-color: #ffcc00; /* Yellow header */
            padding: 20px 0;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
        }

        /* Main Section */
        main {
            padding: 40px;
            text-align: center;
        }

        form {
            background: rgba(255, 255, 255, 0.1); /* Lightened background for form */
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            margin: 0 auto;
        }

        form .form-group {
            margin-bottom: 15px;
        }

        form .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        form .form-group input, form .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form .form-group button {
            background-color: #ffcc00;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form .form-group button:hover {
            background-color: #e6b800;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #222; /* Dark footer */
            color: #777; /* Lighter text color */
        }
    </style>
</head>
<body>
    <header>
        <h1>Contact Us</h1>
    </header>
    <main>
        <form method="POST" action="process_contact.php">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Send Message</button>
            </div>
        </form>
    </main>
    <footer>
        <p>&copy; 2023 School Management System. All rights reserved.</p>
    </footer>
</body>
</html>
