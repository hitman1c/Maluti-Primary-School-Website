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
