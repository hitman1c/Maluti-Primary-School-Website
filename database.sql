CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT,
    teacher_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    term VARCHAR(50),
    amount DECIMAL(10,2),
    status ENUM('paid', 'pending', 'overdue'),
    payment_date DATE
);

CREATE TABLE IF NOT EXISTS classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    teacher_id INT,
    schedule TEXT
);

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'parent') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    verification_token VARCHAR(64),
    email_verified BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    class VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    subject VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS parents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS user_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_role VARCHAR(50),
    activity TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    event_type ENUM('Class', 'Meeting', 'Sports Day', 'Lunch Week') NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    grade ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id)
);

CREATE TABLE IF NOT EXISTS replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
);
-- Create backup table for fees
CREATE TABLE IF NOT EXISTS fees_backup (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount_due DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    backup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create backup table for students (if not already created)
CREATE TABLE IF NOT EXISTS students_backup (
    id INT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    class VARCHAR(50),
    admission_date DATE,
    backup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Generate parent_student insert statements with correct parent and student IDs

SELECT CONCAT('INSERT INTO parent_student (parent_id, student_id) VALUES (',
    p.id, ', ',
    s.id, ');')
FROM parents p
JOIN students s ON p.user_id = s.user_id
ORDER BY p.id, s.id;

-- Run this query to get the correct insert statements for parent_student table



-- Example data for testing
INSERT INTO grades (student_id, assignment_id, grade) VALUES
(1, 1, 'A'),
(1, 2, 'B'),
(2, 1, 'C'),
(2, 2, 'A');

DROP TRIGGER IF EXISTS set_admin_verified;
CREATE TRIGGER set_admin_verified 
BEFORE INSERT ON users
FOR EACH ROW 
SET NEW.email_verified = CASE WHEN NEW.role = 'admin' THEN TRUE ELSE FALSE END;

-- Add default admin if not exists
INSERT INTO users (username, password, email, role, email_verified) 
VALUES ('admin', '$2y$10$8zM7ZfXbwQVE0.uG3/czpexUEr4F.dV2zS9NZqIF2jyGkNGGJZb3e', 'admin@school.com', 'admin', true)
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$8zM7ZfXbwQVE0.uG3/czpexUEr4F.dV2zS9NZqIF2jyGkNGGJZb3e',
    email = 'admin@school.com';

ALTER TABLE assignments
ADD COLUMN reviewed BOOLEAN DEFAULT 0 AFTER due_date;
-- Check if the column 'student_id' exists before adding it
ALTER TABLE students
ADD COLUMN IF NOT EXISTS student_id VARCHAR(50) NULL;

INSERT INTO students (id, first_name, last_name, class, student_id) VALUES
(1, 'SECHABA', 'RELEBOHILE', 'Class A', 'STU001'),(2, 'SEABATA', 'HLAPISO', 'Class B', 'STU002'),
(3, 'MPHO', 'PALESA', 'Class A', 'STU003'),(4, 'KANANELO', 'LERO', 'Class C', 'STU004'),(5, 'THAOBO', 'MOKOENA', 'Class A', 'STU005'),
(6, 'LEBOHANG', 'MAKHAHLE', 'Class B', 'STU006'),(7, 'THAITO', 'SEKHONYANA', 'Class C', 'STU007'),
(8, 'LESEYGO', 'MOTLOUNG', 'Class A', 'STU008'),(9, 'THABGO', 'MAKHANYA', 'Class B', 'STU009'),
(10, 'NTHAABELENG', 'MASHABA', 'Class C', 'STU010'),(11, 'TSEROPO', 'MOHLALA', 'Class A', 'STU011'),
(12, 'BOITUMELO', 'MOFUTSANAYANA', 'Class B', 'STU012'),(13, 'KAGISO', 'MOKOATSI', 'Class C', 'STU013'),
(14, 'NALEDI', 'MASHILO', 'Class A', 'STU014'),(15, 'THABO', 'MOKOENA', 'Class B', 'STU015'),
(16, 'LEBOHANG', 'MAKHAHLE', 'Class C', 'STU016'),(17, 'THATUO', 'SEKHONYANA', 'Class A', 'STU017'),
(18, 'LEEGO', 'MOTLOUNG', 'Class B', 'STU018'),(19, 'TABO', 'MAKHANYA', 'Class C', 'STU019'),
(20, 'THABELENG', 'MASHABA', 'Class A', 'STU020'),(21, 'TSHEPO', 'MOHLALA', 'Class B', 'STU021'),
(22, 'KOITUMELO', 'MOFUTSANAYANA', 'Class C', 'STU022'),(23, 'KAGISO', 'MOKOATSI', 'Class A', 'STU023'),
(24, 'PALEDI', 'MASHILO', 'Class B', 'STU024');
-- Populate parent_student linking table with valid parent and student IDs from sample_data_part2.sql

INSERT INTO parent_student (parent_id, student_id) VALUES
(71, 21),
(72, 40),
(73, 41),
(74, 42),
(75, 43),
(76, 44),
(77, 45),
(78, 26),
(79, 27),
(80, 28);

-- Add more links as needed based on actual parent and student IDs
-- Insert teacher users
INSERT INTO users (username, password, email, role, email_verified, access_code) VALUES
('mofokeng', '$2y$10$examplehash1', 'mofokeng@school.com', 'teacher', TRUE, '1234'),
('mokoena', '$2y$10$examplehash2', 'mokoena@school.com', 'teacher', TRUE, '2345'),
('sekhonyana', '$2y$10$examplehash3', 'sekhonyana@school.com', 'teacher', TRUE, '3456'),
('motloung', '$2y$10$examplehash4', 'motloung@school.com', 'teacher', TRUE, '4567'),
('makhanya', '$2y$10$examplehash5', 'makhanya@school.com', 'teacher', TRUE, '5678'),
('mashaba', '$2y$10$examplehash6', 'mashaba@school.com', 'teacher', TRUE, '6789'),
('mohlala', '$2y$10$examplehash7', 'mohlala@school.com', 'teacher', TRUE, '7890'),
('mofutsanyana', '$2y$10$examplehash8', 'mofutsanyana@school.com', 'teacher', TRUE, '8901'),
('mokoatsi', '$2y$10$examplehash9', 'mokoatsi@school.com', 'teacher', TRUE, '9012'),
('mashilo', '$2y$10$examplehash10', 'mashilo@school.com', 'teacher', TRUE, '0123');

-- Insert teachers details using the last inserted user IDs
INSERT INTO teachers (user_id, first_name, last_name, subject)
SELECT id, first_name, last_name, subject FROM (
    SELECT id, 'Mpho' AS first_name, 'Mofokeng' AS last_name, 'Mathematics' AS subject FROM users WHERE username = 'mofokeng'
    UNION ALL
    SELECT id, 'Thabo', 'Mokoena', 'Mathematics' FROM users WHERE username = 'mokoena'
    UNION ALL
    SELECT id, 'Lerato', 'Sekhonyana', 'Sesotho' FROM users WHERE username = 'sekhonyana'
    UNION ALL
    SELECT id, 'Kabelo', 'Motloung', 'Sesotho' FROM users WHERE username = 'motloung'
    UNION ALL
    SELECT id, 'Palesa', 'Makhanya', 'English' FROM users WHERE username = 'makhanya'
    UNION ALL
    SELECT id, 'Neo', 'Mashaba', 'English' FROM users WHERE username = 'mashaba'
    UNION ALL
    SELECT id, 'Tshepo', 'Mohlala', 'Mathematics' FROM users WHERE username = 'mohlala'
    UNION ALL
    SELECT id, 'Boitumelo', 'Mofutsanyana', 'Sesotho' FROM users WHERE username = 'mofutsanyana'
    UNION ALL
    SELECT id, 'Kagiso', 'Mokoatsi', 'English' FROM users WHERE username = 'mokoatsi'
    UNION ALL
    SELECT id, 'Naledi', 'Mashilo', 'Mathematics' FROM users WHERE username = 'mashilo'
) AS temp;

-- Insert 7 classes
INSERT INTO classes (name, teacher_id, schedule) VALUES
('Mathematics 1', NULL, ''),
('Mathematics 2', NULL, ''),
('Sesotho 1', NULL, ''),
('Sesotho 2', NULL, ''),
('English 1', NULL, ''),
('English 2', NULL, ''),
('General Studies', NULL, '');

-- Insert 10 teachers, each assigned to one subject with Sesotho names
INSERT INTO users (username, password, email, role, email_verified) VALUES
('mofokeng', '$2y$10$examplehash', 'mofokeng@school.com', 'teacher', TRUE),
('mokoena', '$2y$10$examplehash', 'mokoena@school.com', 'teacher', TRUE),
('sekhonyana', '$2y$10$examplehash', 'sekhonyana@school.com', 'teacher', TRUE),
('motloung', '$2y$10$examplehash', 'motloung@school.com', 'teacher', TRUE),
('makhanya', '$2y$10$examplehash', 'makhanya@school.com', 'teacher', TRUE),
('mashaba', '$2y$10$examplehash', 'mashaba@school.com', 'teacher', TRUE),
('mohlala', '$2y$10$examplehash', 'mohlala@school.com', 'teacher', TRUE),
('mofutsanyana', '$2y$10$examplehash', 'mofutsanyana@school.com', 'teacher', TRUE),
('mokoatsi', '$2y$10$examplehash', 'mokoatsi@school.com', 'teacher', TRUE),
('mashilo', '$2y$10$examplehash', 'mashilo@school.com', 'teacher', TRUE);

-- Insert teachers details with subjects (Mathematics, Sesotho, English)
INSERT INTO teachers (user_id, first_name, last_name, subject) VALUES
(1, 'Mpho', 'Mofokeng', 'Mathematics'),
(2, 'Thabo', 'Mokoena', 'Mathematics'),
(3, 'Lerato', 'Sekhonyana', 'Sesotho'),
(4, 'Kabelo', 'Motloung', 'Sesotho'),
(5, 'Palesa', 'Makhanya', 'English'),
(6, 'Neo', 'Mashaba', 'English'),
(7, 'Tshepo', 'Mohlala', 'Mathematics'),
(8, 'Boitumelo', 'Mofutsanyana', 'Sesotho'),
(9, 'Kagiso', 'Mokoatsi', 'English'),
(10, 'Naledi', 'Mashilo', 'Mathematics');

-- Insert 7 classes
INSERT INTO classes (name, teacher_id, schedule) VALUES
('Mathematics 1', NULL, ''),
('Mathematics 2', NULL, ''),
('Sesotho 1', NULL, ''),
('Sesotho 2', NULL, ''),
('English 1', NULL, ''),
('English 2', NULL, ''),
('General Studies', NULL, '');

-- Insert 20 teachers, each assigned to one subject
INSERT INTO users (username, password, email, role, email_verified) VALUES
('teacher1', '$2y$10$examplehash', 'teacher1@school.com', 'teacher', TRUE),
('teacher2', '$2y$10$examplehash', 'teacher2@school.com', 'teacher', TRUE),
('teacher3', '$2y$10$examplehash', 'teacher3@school.com', 'teacher', TRUE),
('teacher4', '$2y$10$examplehash', 'teacher4@school.com', 'teacher', TRUE),
('teacher5', '$2y$10$examplehash', 'teacher5@school.com', 'teacher', TRUE),
('teacher6', '$2y$10$examplehash', 'teacher6@school.com', 'teacher', TRUE),
('teacher7', '$2y$10$examplehash', 'teacher7@school.com', 'teacher', TRUE),
('teacher8', '$2y$10$examplehash', 'teacher8@school.com', 'teacher', TRUE),
('teacher9', '$2y$10$examplehash', 'teacher9@school.com', 'teacher', TRUE),
('teacher10', '$2y$10$examplehash', 'teacher10@school.com', 'teacher', TRUE),
('teacher11', '$2y$10$examplehash', 'teacher11@school.com', 'teacher', TRUE),
('teacher12', '$2y$10$examplehash', 'teacher12@school.com', 'teacher', TRUE),
('teacher13', '$2y$10$examplehash', 'teacher13@school.com', 'teacher', TRUE),
('teacher14', '$2y$10$examplehash', 'teacher14@school.com', 'teacher', TRUE),
('teacher15', '$2y$10$examplehash', 'teacher15@school.com', 'teacher', TRUE),
('teacher16', '$2y$10$examplehash', 'teacher16@school.com', 'teacher', TRUE),
('teacher17', '$2y$10$examplehash', 'teacher17@school.com', 'teacher', TRUE),
('teacher18', '$2y$10$examplehash', 'teacher18@school.com', 'teacher', TRUE),
('teacher19', '$2y$10$examplehash', 'teacher19@school.com', 'teacher', TRUE),
('teacher20', '$2y$10$examplehash', 'teacher20@school.com', 'teacher', TRUE);

-- Insert teachers details with subjects (Mathematics, Sesotho, English)
INSERT INTO teachers (user_id, first_name, last_name, subject) VALUES
(1, 'Teacher', 'One', 'Mathematics'),
(2, 'Teacher', 'Two', 'Mathematics'),
(3, 'Teacher', 'Three', 'Sesotho'),
(4, 'Teacher', 'Four', 'Sesotho'),
(5, 'Teacher', 'Five', 'English'),
(6, 'Teacher', 'Six', 'English'),
(7, 'Teacher', 'Seven', 'Mathematics'),
(8, 'Teacher', 'Eight', 'Sesotho'),
(9, 'Teacher', 'Nine', 'English'),
(10, 'Teacher', 'Ten', 'Mathematics'),
(11, 'Teacher', 'Eleven', 'Sesotho'),
(12, 'Teacher', 'Twelve', 'English'),
(13, 'Teacher', 'Thirteen', 'Mathematics'),
(14, 'Teacher', 'Fourteen', 'Sesotho'),
(15, 'Teacher', 'Fifteen', 'English'),
(16, 'Teacher', 'Sixteen', 'Mathematics'),
(17, 'Teacher', 'Seventeen', 'Sesotho'),
(18, 'Teacher', 'Eighteen', 'English'),
(19, 'Teacher', 'Nineteen', 'Mathematics'),
(20, 'Teacher', 'Twenty', 'Sesotho');

-- Insert 50 students and parents, all students take all subjects
INSERT INTO users (username, password, email, role, email_verified) VALUES
('student1', '$2y$10$examplehash', 'student1@school.com', 'student', TRUE),
('student2', '$2y$10$examplehash', 'student2@school.com', 'student', TRUE),
-- ... continue for 50 students
('parent1', '$2y$10$examplehash', 'parent1@school.com', 'parent', TRUE),
('parent2', '$2y$10$examplehash', 'parent2@school.com', 'parent', TRUE);
-- ... continue for 50 parents

-- Insert students details
INSERT INTO students (user_id, first_name, last_name, class) VALUES
(21, 'Student', 'One', 'Mathematics 1'),
(22, 'Student', 'Two', 'Mathematics 1');
-- ... continue for all students with classes Mathematics 1, Sesotho 1, English 1, etc.

-- Insert parents details
INSERT INTO parents (user_id, first_name, last_name, phone) VALUES
(71, 'Parent', 'One', '1234567890'),
(72, 'Parent', 'Two', '1234567890');
-- ... continue for all parents

-- Insert meetings, lunch, sports into classes or schedule as needed
-- This is a sample, you can expand as needed
INSERT INTO classes (name, teacher_id, schedule) VALUES
('Meeting', NULL, '2025-05-01 09:00:00'),
('Lunch', NULL, '2025-05-01 12:00:00'),
('Sports', NULL, '2025-05-01 15:00:00');
