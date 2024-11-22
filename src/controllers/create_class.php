<?php
session_start();

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Check if the user exists
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
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
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Check if the email is already registered
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param('ssss', $name, $email, $password_hash, $role);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now log in.";
            } else {
                $error = "Failed to register. Please try again.";
            }
        } else {
            $error = "Email is already registered.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Authentication</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Login</h2>
            <form action="auth.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-lg font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-3 rounded bg-gray-800 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-lg font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 rounded bg-gray-800 text-white" required>
                </div>
                <button type="submit" name="login" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Login</button>
            </form>
        </div>

        <div class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Register</h2>
            <form action="auth.php" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-lg font-medium mb-2">Name</label>
                    <input type="text" id="name" name="name" class="w-full p-3 rounded bg-gray-800 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-3 rounded bg-gray-800 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-lg font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 rounded bg-gray-800 text-white" required>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-lg font-medium mb-2">Role</label>
                    <select id="role" name="role" class="w-full p-3 rounded bg-gray-800 text-white" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
                <button type="submit" name="register" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Register</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>