<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../public/login.php');
    exit;
}

// Redirect based on user role
if ($_SESSION['user_role'] === 'teacher') {
    header('Location: ../public/teacher_dashboard.php');
    exit;
} else if ($_SESSION['user_role'] === 'student') {
    header('Location: ../punlic/student_dashboard.php');
    exit;
} else {
    // If the role is not recognized, log out the user
    session_destroy();
    header('Location: ../public/login.php');
    exit;
}
?>