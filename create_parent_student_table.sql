-- Create parent_student linking table to associate parents with their children
CREATE TABLE IF NOT EXISTS parent_student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    student_id INT NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_parent_student (parent_id, student_id)
);

-- Sample data: link parent with id 1 to students with ids 1 and 2
INSERT INTO parent_student (parent_id, student_id) VALUES
(1, 1),
(1, 2);

-- Add more parent-child links as needed
