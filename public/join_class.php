<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['user_role'] !== 'student') {
    header('Location: login.php');
    exit;
}

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';
require_once '../database/db_config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code = trim($_POST['class_code']);

    // Validate input
    if (empty($class_code)) {
        $error = "Class code is required.";
    } else {
        // Check if the class exists
        $query = "SELECT id FROM classes WHERE class_code = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $class_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $class = $result->fetch_assoc();
            $class_id = $class['id'];
            $student_id = $_SESSION['user_id'];

            // Check if the student is already enrolled in the class
            $check_query = "SELECT * FROM class_students WHERE class_id = ? AND student_id = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bind_param('dd', $class_id, $student_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "You are already enrolled in this class.";
                header('Location: student_dashboard.php');
            } else {
                // Enroll the student in the class
                $insert_query = "INSERT INTO class_students (class_id, student_id) VALUES (?, ?)";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->bind_param('dd', $class_id, $student_id);

                if ($insert_stmt->execute()) {
                    $success = "Successfully joined the class.";
                    header('Location: student_dashboard.php');
                } else {
                    $error = "Failed to join the class. Please try again.";
                }
            }
        } else {
            $error = "Invalid class code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh]">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Join a Class</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="join_class.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="class_code" class="block text-lg font-medium mb-2">Class Code</label>
                <input type="text" id="class_code" name="class_code" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Join Class</button>
        </form>
    </div>
</body>
</html>
<?php include 'includes/footer.php'; ?>   