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
