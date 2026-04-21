CREATE TABLE replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
);
