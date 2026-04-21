-- Insert remaining 10 teachers with Sesotho names
INSERT INTO users (username, password, email, role, email_verified) VALUES
('mofolo', '$2y$10$examplehash', 'mofolo@school.com', 'teacher', TRUE),
('mohlomi', '$2y$10$examplehash', 'mohlomi@school.com', 'teacher', TRUE),
('mofeta', '$2y$10$examplehash', 'mofeta@school.com', 'teacher', TRUE),
('mohlankoe', '$2y$10$examplehash', 'mohlankoe@school.com', 'teacher', TRUE),
('mofokeng2', '$2y$10$examplehash', 'mofokeng2@school.com', 'teacher', TRUE),
('mokoena2', '$2y$10$examplehash', 'mokoena2@school.com', 'teacher', TRUE),
('sekhonyana2', '$2y$10$examplehash', 'sekhonyana2@school.com', 'teacher', TRUE),
('motloung2', '$2y$10$examplehash', 'motloung2@school.com', 'teacher', TRUE),
('makhanya2', '$2y$10$examplehash', 'makhanya2@school.com', 'teacher', TRUE),
('mashaba2', '$2y$10$examplehash', 'mashaba2@school.com', 'teacher', TRUE);

-- Insert teachers details with subjects
INSERT INTO teachers (user_id, first_name, last_name, subject) VALUES
(11, 'Refilwe', 'Mofolo', 'Sesotho'),
(12, 'Karabo', 'Mohlomi', 'English'),
(13, 'Lesedi', 'Mofeta', 'Mathematics'),
(14, 'Pitso', 'Mohlankoe', 'Sesotho'),
(15, 'Masego', 'Mofokeng', 'English'),
(16, 'Khotso', 'Mokoena', 'Mathematics'),
(17, 'Tshepiso', 'Sekhonyana', 'Sesotho'),
(18, 'Nthabiseng', 'Motloung', 'English'),
(19, 'Mokete', 'Makhanya', 'Mathematics'),
(20, 'Keneiloe', 'Mashaba', 'Sesotho');

-- Insert 25 students and parents with Sesotho names (part 1)
INSERT INTO users (username, password, email, role, email_verified) VALUES
('student1', '$2y$10$examplehash', 'student1@school.com', 'student', TRUE),
('student2', '$2y$10$examplehash', 'student2@school.com', 'student', TRUE),
('student3', '$2y$10$examplehash', 'student3@school.com', 'student', TRUE),
('student4', '$2y$10$examplehash', 'student4@school.com', 'student', TRUE),
('student5', '$2y$10$examplehash', 'student5@school.com', 'student', TRUE),
('student6', '$2y$10$examplehash', 'student6@school.com', 'student', TRUE),
('student7', '$2y$10$examplehash', 'student7@school.com', 'student', TRUE),
('student8', '$2y$10$examplehash', 'student8@school.com', 'student', TRUE),
('student9', '$2y$10$examplehash', 'student9@school.com', 'student', TRUE),
('student10', '$2y$10$examplehash', 'student10@school.com', 'student', TRUE),
('parent1', '$2y$10$examplehash', 'parent1@school.com', 'parent', TRUE),
('parent2', '$2y$10$examplehash', 'parent2@school.com', 'parent', TRUE),
('parent3', '$2y$10$examplehash', 'parent3@school.com', 'parent', TRUE),
('parent4', '$2y$10$examplehash', 'parent4@school.com', 'parent', TRUE),
('parent5', '$2y$10$examplehash', 'parent5@school.com', 'parent', TRUE),
('parent6', '$2y$10$examplehash', 'parent6@school.com', 'parent', TRUE),
('parent7', '$2y$10$examplehash', 'parent7@school.com', 'parent', TRUE),
('parent8', '$2y$10$examplehash', 'parent8@school.com', 'parent', TRUE),
('parent9', '$2y$10$examplehash', 'parent9@school.com', 'parent', TRUE),
('parent10', '$2y$10$examplehash', 'parent10@school.com', 'parent', TRUE);

-- Insert students details (part 1)
INSERT INTO students (user_id, first_name, last_name, class) VALUES
(21, 'Kabelo', 'Mokoena', 'Mathematics 1'),
(22, 'Lerato', 'Sekhonyana', 'Mathematics 1'),
(23, 'Palesa', 'Makhanya', 'Sesotho 1'),
(24, 'Neo', 'Mashaba', 'Sesotho 1'),
(25, 'Tshepo', 'Mohlala', 'English 1'),
(26, 'Boitumelo', 'Mofutsanyana', 'English 1'),
(27, 'Kagiso', 'Mokoatsi', 'Mathematics 2'),
(28, 'Naledi', 'Mashilo', 'Mathematics 2'),
(29, 'Refilwe', 'Mofolo', 'Sesotho 2'),
(30, 'Karabo', 'Mohlomi', 'Sesotho 2'),
(31, 'Lesedi', 'Mofeta', 'English 2'),
(32, 'Pitso', 'Mohlankoe', 'English 2'),
(33, 'Masego', 'Mofokeng', 'Mathematics 1'),
(34, 'Khotso', 'Mokoena', 'Mathematics 1'),
(35, 'Tshepiso', 'Sekhonyana', 'Sesotho 1'),
(36, 'Nthabiseng', 'Motloung', 'Sesotho 1'),
(37, 'Mokete', 'Makhanya', 'English 1'),
(38, 'Keneiloe', 'Mashaba', 'English 1'),
(39, 'Mpho', 'Mofokeng', 'Mathematics 2'),
(40, 'Thabo', 'Mokoena', 'Mathematics 2'),
(41, 'Lerato', 'Sekhonyana', 'Sesotho 2'),
(42, 'Kabelo', 'Motloung', 'Sesotho 2'),
(43, 'Palesa', 'Makhanya', 'English 2'),
(44, 'Neo', 'Mashaba', 'English 2'),
(45, 'Tshepo', 'Mohlala', 'Mathematics 1');

-- Insert parents details (part 1)
INSERT INTO parents (user_id, first_name, last_name, phone) VALUES
(71, 'Mpho', 'Mofokeng', '1234567890'),
(72, 'Thabo', 'Mokoena', '1234567890'),
(73, 'Lerato', 'Sekhonyana', '1234567890'),
(74, 'Kabelo', 'Motloung', '1234567890'),
(75, 'Palesa', 'Makhanya', '1234567890'),
(76, 'Neo', 'Mashaba', '1234567890'),
(77, 'Tshepo', 'Mohlala', '1234567890'),
(78, 'Boitumelo', 'Mofutsanyana', '1234567890'),
(79, 'Kagiso', 'Mokoatsi', '1234567890'),
(80, 'Naledi', 'Mashilo', '1234567890');
