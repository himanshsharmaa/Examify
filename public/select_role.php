<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';
require_once '../database/db_config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role']);
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($role) || !in_array($role, ['student', 'teacher'])) {
        $error = "Role is required and must be either 'student' or 'teacher'.";
    } else {
        // Update user role in the database
        $query = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sd', $role, $user_id);

        if ($stmt->execute()) {
            $_SESSION['user_role'] = $role;

            // Redirect based on user role
            if ($role === 'teacher') {
                header('Location: create_class.php');
            } else {
                header('Location: join_class.php');
            }
            exit;
        } else {
            $error = "Failed to update role. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white flex justify-center items-center min-h-screen">

    <!-- Role Selection Form Container -->
    <div class="bg-[#0f2027] p-8 rounded-lg shadow-lg w-full sm:w-96">
        <h2 class="text-3xl font-bold text-center text-[#d4af37] mb-6">See what you can do!</h2>

        <!-- Error Messages -->
        <?php
        if (isset($error)) {
            echo '<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">';
            echo "<p>$error</p>";
            echo '</div>';
        }
        ?>

        <!-- Role Selection Form -->
        <form id="role-form" action="select_role.php" method="POST">
            <div class="mb-4">
                <button type="submit" name="role" value="teacher" class="w-full py-2 mt-4 bg-[#d4af37] text-gray-900 font-bold rounded-lg hover:bg-[#ffcc00] transition duration-300">
                    Create a Class
                </button>
                <p class="mt-2 text-sm text-gray-400">As a teacher, you can create a class, manage students, and organize exams.</p>
            </div>

            <div class="mb-4">
                <button type="submit" name="role" value="student" class="w-full py-2 mt-4 bg-[#d4af37] text-gray-900 font-bold rounded-lg hover:bg-[#ffcc00] transition duration-300">
                    Join a Class
                </button>
                <p class="mt-2 text-sm text-gray-400">As a student, you can join a class, participate in exams, and track your progress.</p>
            </div>
        </form>
    </div>

</body>
</html>