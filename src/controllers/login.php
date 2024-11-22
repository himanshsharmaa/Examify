// login.php (located in src/controllers/login.php)

<?php
session_start();
include('../../config/constants.php');
include('../../database/db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['errors'][] = "No user found with this email address.";
        header("Location: /login.php");
        exit;
    }

    $user = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: /dashboard.php");
    } else {
        $_SESSION['errors'][] = "Invalid password.";
        header("Location: /login.php");
    }
}
?>
