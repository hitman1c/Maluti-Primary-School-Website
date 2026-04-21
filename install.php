<?php
require_once "config.php";

try {
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    mysqli_query($conn, $sql);
    
    // Select database
    mysqli_select_db($conn, DB_NAME);
    
    // Read SQL file
    $queries = explode(';', file_get_contents('database.sql'));
    
    // Execute each query separately
    foreach($queries as $query) {
        $query = trim($query);
        if(!empty($query)) {
            if(!mysqli_query($conn, $query)) {
                throw new Exception("Error executing query: " . mysqli_error($conn));
            }
            // Clear results
            while(mysqli_more_results($conn) && mysqli_next_result($conn)) {
                if($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            }
        }
    }
    
    // Create default admin
    $username = "admin";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $email = "admin@school.com";
    $role = "admin";
    
    $sql = "INSERT INTO users (username, password, email, role) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE username=username";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $role);
    mysqli_stmt_execute($stmt);
    
    echo "<div style='text-align:center;margin-top:50px;'>";
    echo "<h2>Installation Successful!</h2>";
    echo "<p>Admin Account Created:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123</p>";
    echo "<a href='index.php'>Go to Homepage</a>";
    echo "</div>";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($conn);
?>
