<?php                                                                                                                                                
session_start();
require_once "config.php";

if ($_SESSION["role"] !== "parent") {
    header("location: login.php");
    exit;
}

if (isset($_POST['update_profile'])) {
    $parent_id = $_SESSION["user_id"];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE parents SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $name, $email, $password_hash, $parent_id);
    } else {
        $query = "UPDATE parents SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $name, $email, $parent_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update profile.";
    }

    header("location: parent_dashboard.php");
    exit;
}
?>
                 