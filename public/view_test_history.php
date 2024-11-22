<?php include 'includes/header.php'; ?>
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

// Get the test ID from the URL
$test_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch test details from the database
$query = "
    SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
    FROM exams
    JOIN classes ON exams.class_id = classes.id
    WHERE exams.id = ? AND classes.teacher_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('dd', $test_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: teacher_dashboard.php');
    exit;
}

$test = $result->fetch_assoc();

// Fetch test questions from the database
$query = "
    SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option
    FROM questions
    WHERE exam_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $test_id);
$stmt->execute();
$questions = $stmt->get_result();

// Fetch test history from the database
$query = "
    SELECT results.id, results.score, users.name, users.email
    FROM results
    JOIN users ON results.student_id = users.id
    WHERE results.exam_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $test_id);
$stmt->execute();
$test_history = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Test History - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh] flex flex-col justify-center items-center">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Test History: <?php echo htmlspecialchars($test['exam_name']); ?></h1>
        <p class="text-lg mb-8">Class: <?php echo htmlspecialchars($test['class_name']); ?></p>
        <p class="text-lg mb-8">Date: <?php echo htmlspecialchars($test['exam_date']); ?></p>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Questions and Correct Answers</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg mb-8">
            <?php while ($question = $questions->fetch_assoc()): ?>
                <li class="mb-2">
                    <p class="font-bold"><?php echo htmlspecialchars($question['question_text']); ?></p>
                    <p class="text-gray-400">A: <?php echo htmlspecialchars($question['option_a']); ?></p>
                    <p class="text-gray-400">B: <?php echo htmlspecialchars($question['option_b']); ?></p>
                    <p class="text-gray-400">C: <?php echo htmlspecialchars($question['option_c']); ?></p>
                    <p class="text-gray-400">D: <?php echo htmlspecialchars($question['option_d']); ?></p>
                    <p class="text-gray-400">Correct Option: <?php echo htmlspecialchars($question['correct_option']); ?></p>
                </li>
            <?php endwhile; ?>
        </ul>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Students Who Attempted the Test</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg">
            <?php while ($history = $test_history->fetch_assoc()): ?>
                <li class="mb-2">
                    <p class="font-bold"><?php echo htmlspecialchars($history['name']); ?></p>
                    <p class="text-gray-400">Email: <?php echo htmlspecialchars($history['email']); ?></p>
                    <p class="text-gray-400">Score: <?php echo htmlspecialchars($history['score']); ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
<?php include 'includes/footer.php'; ?>