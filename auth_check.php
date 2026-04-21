<?php
function checkAuth($required_role = null) {
    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: login.php");
        exit();
    }
    
    if($required_role !== null && $_SESSION['role'] !== $required_role) {
        header("location: login.php");
        exit();
    }
}
?>
