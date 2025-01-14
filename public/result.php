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

// Get the result ID from the URL
$result_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch test result details from the database
$query = "
    SELECT results.id, results.score, exams.exam_name, exams.exam_date, classes.class_name, users.name AS student_name
    FROM results
    JOIN exams ON results.exam_id = exams.id
    JOIN classes ON exams.class_id = classes.id
    JOIN users ON results.student_id = users.id
    WHERE results.id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $result_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: 404.php');
    exit;
}

$test_result = $result->fetch_assoc();

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh] flex flex-col">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Test Results</h1>
        <p class="text-lg mb-8">Student: <?php echo htmlspecialchars($test_result['student_name']); ?></p>
        <p class="text-lg mb-8">Test: <?php echo htmlspecialchars($test_result['exam_name']); ?></p>
        <p class="text-lg mb-8">Class: <?php echo htmlspecialchars($test_result['class_name']); ?></p>
        <p class="text-lg mb-8">Date: <?php echo htmlspecialchars($test_result['exam_date']); ?></p>
        <p class="text-lg mb-8">Score: <?php echo htmlspecialchars($test_result['score']); ?></p>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>