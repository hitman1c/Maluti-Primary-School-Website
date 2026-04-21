<?php
session_start();
include 'auth.php';
include 'db_connection.php';

checkRoleWithCode('parent');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zip = trim($_POST['zip'] ?? '');

    $errors = [];

    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }
    if (empty($last_name)) {
        $errors[] = 'Last name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }

    if (count($errors) === 0) {
        // Get parent id
        $parent_res = mysqli_query($conn, "SELECT id FROM parents WHERE user_id = $user_id LIMIT 1");
        $parent = mysqli_fetch_assoc($parent_res);
        $parent_id = $parent['id'] ?? null;

        if ($parent_id) {
            // Update parent profile
            $stmt = mysqli_prepare($conn, "UPDATE parents SET first_name=?, last_name=?, email=?, phone=?, address=?, city=?, state=?, zip=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssssssi', $first_name, $last_name, $email, $phone, $address, $city, $state, $zip, $parent_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                $_SESSION['profile_update_success'] = 'Profile updated successfully.';
                header('Location: parent_dashboard.php');
                exit();
            } else {
                $errors[] = 'Failed to update profile. Please try again.';
            }
        } else {
            $errors[] = 'Parent profile not found.';
        }
    }
} else {
    $errors = ['Invalid request method.'];
}

// If errors, store in session and redirect back
if (!empty($errors)) {
    $_SESSION['profile_update_errors'] = $errors;
    header('Location: parent_dashboard.php');
    exit();
}
?>
