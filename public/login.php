<?php
session_start();

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';
require_once '../database/db_config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if the user exists in the database
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "No user found with this email address.";
        } else {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] === 'teacher') {
                    header('Location: teacher_dashboard.php');
                } else {
                    header('Location: student_dashboard.php');
                }
                exit;
            } else {
                $errors[] = "Invalid password.";
            }
        }
    }

    // Store errors in session and redirect back to the form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white flex justify-center items-center min-h-screen">

    <!-- Login Form Container -->
    <div class="bg-[#0f2027] p-8 rounded-lg shadow-lg w-full sm:w-96">
        <h2 class="text-3xl font-bold text-center text-[#d4af37] mb-6">Login to Your Account</h2>

        <!-- Error Messages -->
        <?php
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            echo '<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">';
            echo '<ul>';
            foreach ($_SESSION['errors'] as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
            echo '</div>';
            unset($_SESSION['errors']);
        }
        ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="w-full mt-2 p-2 bg-gray-800 text-white border border-gray-600 rounded-md focus:ring-2 focus:ring-[#d4af37] focus:outline-none"
                    required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full mt-2 p-2 bg-gray-800 text-white border border-gray-600 rounded-md focus:ring-2 focus:ring-[#d4af37] focus:outline-none"
                    required>
            </div>

            <button 
                type="submit" 
                class="w-full py-2 mt-4 bg-[#d4af37] text-gray-900 font-bold rounded-lg hover:bg-[#ffcc00] transition duration-300">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-gray-400">Don't have an account? 
            <a href="signup.php" class="text-[#d4af37] hover:underline">Sign up</a>
        </p>
    </div>

</body>
</html>