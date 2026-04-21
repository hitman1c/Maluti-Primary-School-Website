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
