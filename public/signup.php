<?php
session_start();

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';
require_once '../database/db_config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate input
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long and include at least one letter, one number, and one special character.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if the email is already registered
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
    }

    // If no errors, insert the new user into the database
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sss', $username, $email, $password_hash);

        if ($stmt->execute()) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $stmt->insert_id;
            header('Location: select_role.php');
            exit;
        } else {
            $errors[] = "Failed to register. Please try again.";
        }
    }

    // Store errors in session and redirect back to the form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: signup.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white flex justify-center items-center min-h-screen">

    <!-- Signup Form Container -->
    <div class="bg-[#0f2027] p-8 rounded-lg shadow-lg w-full sm:w-96">
        <h2 class="text-3xl font-bold text-center text-[#d4af37] mb-6">Create Your Account</h2>

        <!-- Error/Success Messages -->
        <div id="error-messages" class="hidden bg-red-100 text-red-700 p-4 rounded-lg mb-4"></div>

        <?php
        // Show server-side errors if any
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            echo '<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">';
            echo '<ul>';
            foreach ($_SESSION['errors'] as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
            echo '</div>';
            unset($_SESSION['errors']);
        } elseif (isset($_GET['success'])) {
            echo '<div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">' . htmlspecialchars($_GET['success']) . '</div>';
        }
        ?>

        <!-- Signup Form -->
        <form id="signup-form" action="signup.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="w-full mt-2 p-2 bg-gray-800 text-white border border-gray-600 rounded-md focus:ring-2 focus:ring-[#d4af37] focus:outline-none"
                    required>
            </div>

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

            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="w-full mt-2 p-2 bg-gray-800 text-white border border-gray-600 rounded-md focus:ring-2 focus:ring-[#d4af37] focus:outline-none"
                    required>
            </div>

            <button 
                type="submit" 
                class="w-full py-2 mt-4 bg-[#d4af37] text-gray-900 font-bold rounded-lg hover:bg-[#ffcc00] transition duration-300">
                Create Account
            </button>
        </form>

        <p class="mt-4 text-center text-gray-400">Already have an account? 
            <a href="login.php" class="text-[#d4af37] hover:underline">Login</a>
        </p>
    </div>

</body>
</html>