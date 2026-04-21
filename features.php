<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - Maluti Primary School</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
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
            background-color: #121212;
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

        /* Main Content */
        main {
            padding: 40px;
            max-width: 1200px;
            margin: auto;
        }

        main h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
        }

        main .section {
            margin-bottom: 40px;
        }

        main .section h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        main .section p {
            margin-bottom: 10px;
        }

        main .section img {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Footer */
        footer {
            background-color: rgba(0, 0, 0, 0.9);
            color: #ffcc00;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        footer p {
            display: inline-block;
            animation: scroll 10s linear infinite;
        }

        @keyframes scroll {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Features</h1>
        <p>Explore the features that make Maluti Primary School unique</p>
    </header>
    <main>
        <h2>Our Features</h2>

        <div class="section">
            <h3>Educational Resources</h3>
            <p>Access to an online course catalog, learning management system (LMS), and study materials to support student success.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Education" alt="Lesotho Education">
        </div>

        <div class="section">
            <h3>Community Engagement</h3>
            <p>Stay connected with our events calendar, parent-teacher communication tools, and community service opportunities.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Community" alt="Lesotho Community">
        </div>

        <div class="section">
            <h3>Student Resources</h3>
            <p>We provide study guides, tutoring services, and career counseling to help students achieve their goals.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Students" alt="Lesotho Students">
        </div>

        <div class="section">
            <h3>Health and Wellness</h3>
            <p>Our school offers mental health resources, nutrition programs, and wellness initiatives to support student well-being.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Health" alt="Lesotho Health">
        </div>

        <div class="section">
            <h3>Extracurricular Activities</h3>
            <p>Join our clubs, sports teams, and volunteer opportunities to develop skills and build friendships.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Sports" alt="Lesotho Sports">
        </div>

        <div class="section">
            <h3>Technology and Innovation</h3>
            <p>Access tech support, digital literacy training, and innovative tools to enhance learning experiences.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Technology" alt="Lesotho Technology">
        </div>

        <div class="section">
            <h3>Safety and Security</h3>
            <p>We prioritize student safety with emergency procedures, safety protocols, and accessibility resources.</p>
            <img src="https://via.placeholder.com/400x250?text=Lesotho+Safety" alt="Lesotho Safety">
        </div>
    </main>
    <footer>
        <p>Upcoming Events: Cultural Day on May 15th | Sports Day on June 10th | Contact us at +266 56171110</p>
    </footer>
</body>
</html>
