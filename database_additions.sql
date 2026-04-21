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
