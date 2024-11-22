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

// Get class ID from the URL
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch class details from the database
$query = "SELECT * FROM classes WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: 404.php');
    exit;
}

$class = $result->fetch_assoc();

// Fetch students enrolled in the class
$query = "SELECT users.id, users.name, users.email FROM class_students JOIN users ON class_students.student_id = users.id WHERE class_students.class_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $class_id);
$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($class['class_name']); ?> - Examify</title>
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
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8"><?php echo htmlspecialchars($class['class_name']); ?></h1>
        <p class="text-lg mb-8"><?php echo htmlspecialchars($class['class_description']); ?></p>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Enrolled Students</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg">
            <?php while ($student = $students->fetch_assoc()): ?>
                <li class="mb-2">
                    <span class="font-bold"><?php echo htmlspecialchars($student['name']); ?></span>
                    <span class="text-gray-400"><?php echo htmlspecialchars($student['email']); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>