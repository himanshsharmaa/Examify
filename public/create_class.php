<?php
session_start();

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['user_role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';
require_once '../database/db_config.php';

// Function to generate a unique class code
function generateClassCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name']);
    $class_description = trim($_POST['class_description']);

    // Validate input
    if (empty($class_name)) {
        $error = "Class name is required.";
    } else {
        // Save the class to the database
        $class_code = generateClassCode(); // Function to generate a unique class code
        $teacher_id = $_SESSION['user_id'];
        $created_at = date('Y-m-d H:i:s');

        $query = "INSERT INTO classes (class_name, class_description, class_code, teacher_id, created_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sssds', $class_name, $class_description, $class_code, $teacher_id, $created_at);

        if ($stmt->execute()) {
            // Redirect to teacher dashboard after successful class creation
            header('Location: teacher_dashboard.php');
            exit;
        } else {
            $error = "Failed to create class. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class - Examify</title>
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
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Create a New Class</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="create_class.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="class_name" class="block text-lg font-medium mb-2">Class Name</label>
                <input type="text" id="class_name" name="class_name" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="class_description" class="block text-lg font-medium mb-2">Class Description</label>
                <textarea id="class_description" name="class_description" class="w-full p-3 rounded bg-gray-800 text-white" rows="4"></textarea>
            </div>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Create Class</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>