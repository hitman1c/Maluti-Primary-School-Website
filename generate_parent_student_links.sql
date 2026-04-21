-- Generate parent_student insert statements with correct parent and student IDs

SELECT CONCAT('INSERT INTO parent_student (parent_id, student_id) VALUES (',
    p.id, ', ',
    s.id, ');')
FROM parents p
JOIN students s ON p.user_id = s.user_id
ORDER BY p.id, s.id;

-- Run this query to get the correct insert statements for parent_student table
