<?php include 'includes/header.php'; ?><?php
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
    SELECT exams.id, exams.exam_name, exams.exam_date, exams.time_limit, classes.class_name
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

// Handle form submission for adding a question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = trim($_POST['correct_option']);

    // Validate input
    if (empty($question_text) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_option)) {
        $error = "All fields are required.";
    } else {
        // Insert new question into the database
        $query = "INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('dssssss', $test_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

        if ($stmt->execute()) {
            $success = "Question added successfully.";
            // Refresh the list of questions
            $query = "
                SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option
                FROM questions
                WHERE exam_id = ?
            ";
            $stmt = $db->prepare($query);
            $stmt->bind_param('d', $test_id);
            $stmt->execute();
            $questions = $stmt->get_result();
        } else {
            $error = "Failed to add question. Please try again.";
        }
    }
}

// Handle form submission for setting the time limit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_time_limit'])) {
    $time_limit = intval($_POST['time_limit']);

    // Update the time limit in the database
    $query = "UPDATE exams SET time_limit = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dd', $time_limit, $test_id);

    if ($stmt->execute()) {
        $success = "Time limit set successfully.";
        // Refresh test details
        $query = "
            SELECT exams.id, exams.exam_name, exams.exam_date, exams.time_limit, classes.class_name
            FROM exams
            JOIN classes ON exams.class_id = classes.id
            WHERE exams.id = ? AND classes.teacher_id = ?
        ";
        $stmt = $db->prepare($query);
        $stmt->bind_param('dd', $test_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $test = $result->fetch_assoc();
    } else {
        $error = "Failed to set time limit. Please try again.";
    }
}

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
    <title>Manage Test - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh] flex flex-col">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Manage Test: <?php echo htmlspecialchars($test['exam_name']); ?></h1>
        <p class="text-lg mb-8">Class: <?php echo htmlspecialchars($test['class_name']); ?></p>
        <p class="text-lg mb-8">Date: <?php echo htmlspecialchars($test['exam_date']); ?></p>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="manage_test.php?id=<?php echo $test_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="time_limit" class="block text-lg font-medium mb-2">Time Limit (minutes)</label>
                <input type="number" id="time_limit" name="time_limit" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($test['time_limit']); ?>" required>
            </div>
            <button type="submit" name="set_time_limit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Set Time Limit</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Add Question</h2>
        <form action="manage_test.php?id=<?php echo $test_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="question_text" class="block text-lg font-medium mb-2">Question Text</label>
                <textarea id="question_text" name="question_text" class="w-full p-3 rounded bg-gray-800 text-white" rows="4" required></textarea>
            </div>
            <div class="mb-4">
                <label for="option_a" class="block text-lg font-medium mb-2">Option A</label>
                <input type="text" id="option_a" name="option_a" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="option_b" class="block text-lg font-medium mb-2">Option B</label>
                <input type="text" id="option_b" name="option_b" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="option_c" class="block text-lg font-medium mb-2">Option C</label>
                <input type="text" id="option_c" name="option_c" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="option_d" class="block text-lg font-medium mb-2">Option D</label>
                <input type="text" id="option_d" name="option_d" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="correct_option" class="block text-lg font-medium mb-2">Correct Option</label>
                <select id="correct_option" name="correct_option" class="w-full p-3 rounded bg-gray-800 text-white" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit" name="add_question" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Add Question</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Questions</h2>
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

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Test History</h2>
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>