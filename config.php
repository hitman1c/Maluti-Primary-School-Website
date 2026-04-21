<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'school_management');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_PORT', 587);

// SMS Gateway Configuration
define('SMS_API_KEY', '1234567890abcdef');
define('SMS_API_URL', 'https://api.smsprovider.com/send');
define('SMS_SENDER_ID', 'SCHOOL');

// Notification Settings
define('ENABLE_EMAIL_NOTIFICATIONS', true);
define('ENABLE_SMS_NOTIFICATIONS', true);
define('NOTIFICATION_TYPES', serialize(['attendance', 'grades', 'fees', 'general']));

// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);
?>
