-- Check if the column 'student_id' exists before adding it
ALTER TABLE students
ADD COLUMN IF NOT EXISTS student_id VARCHAR(50) NULL;

INSERT INTO students (id, first_name, last_name, class, student_id) VALUES
(1, 'John', 'Doe', 'Class A', 'STU001'),
(2, 'Jane', 'Smith', 'Class B', 'STU002'),
(3, 'Alice', 'Johnson', 'Class A', 'STU003'),
(4, 'Bob', 'Brown', 'Class C', 'STU004');
