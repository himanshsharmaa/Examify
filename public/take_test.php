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

// Get the test ID from the URL
$test_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch test details from the database
$query = "
    SELECT exams.id, exams.exam_name, exams.exam_date, exams.time_limit, exams.start_time, classes.class_name
    FROM exams
    JOIN classes ON exams.class_id = classes.id
    WHERE exams.id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $test_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: 404.php');
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

// Handle form submission for test answers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $student_id = $_SESSION['user_id'];
    $answers = $_POST['answers'];

    // Insert the test result
    $query = "INSERT INTO results (exam_id, student_id, score) VALUES (?, ?, 0)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dd', $test_id, $student_id);
    $stmt->execute();
    $result_id = $stmt->insert_id;

    // Insert the answers
    foreach ($answers as $question_id => $selected_option) {
        $query = "INSERT INTO answers (result_id, question_id, selected_option) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('dds', $result_id, $question_id, $selected_option);
        $stmt->execute();
    }

    // Calculate the score
    $query = "
        SELECT q.id, q.correct_option, a.selected_option
        FROM questions q
        JOIN answers a ON q.id = a.question_id
        WHERE a.result_id = ?
    ";
    $stmt = $db->prepare($query);
    $stmt->bind_param('d', $result_id);
    $stmt->execute();
    $answers = $stmt->get_result();

    $score = 0;
    while ($answer = $answers->fetch_assoc()) {
        if ($answer['correct_option'] === $answer['selected_option']) {
            $score++;
        }
    }

    // Update the score in the results table
    $query = "UPDATE results SET score = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dd', $score, $result_id);
    $stmt->execute();

    // Redirect to the result page
    header('Location: result.php?id=' . $result_id);
    exit;
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Test - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh] flex flex-col">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Take Test: <?php echo htmlspecialchars($test['exam_name']); ?></h1>
        <p class="text-lg mb-8">Class: <?php echo htmlspecialchars($test['class_name']); ?></p>
        <p class="text-lg mb-8">Date: <?php echo htmlspecialchars($test['exam_date']); ?></p>

        <form action="take_test.php?id=<?php echo $test_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <?php while ($question = $questions->fetch_assoc()): ?>
                <div class="mb-4">
                    <p class="text-lg font-medium mb-2"><?php echo htmlspecialchars($question['question_text']); ?></p>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_a" name="answers[<?php echo $question['id']; ?>]" value="A" required>
                        <label for="question_<?php echo $question['id']; ?>_a"><?php echo htmlspecialchars($question['option_a']); ?></label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_b" name="answers[<?php echo $question['id']; ?>]" value="B" required>
                        <label for="question_<?php echo $question['id']; ?>_b"><?php echo htmlspecialchars($question['option_b']); ?></label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_c" name="answers[<?php echo $question['id']; ?>]" value="C" required>
                        <label for="question_<?php echo $question['id']; ?>_c"><?php echo htmlspecialchars($question['option_c']); ?></label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_d" name="answers[<?php echo $question['id']; ?>]" value="D" required>
                        <label for="question_<?php echo $question['id']; ?>_d"><?php echo htmlspecialchars($question['option_d']); ?></label>
                    </div>
                </div>
            <?php endwhile; ?>
            <button type="submit" name="submit_test" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Submit Test</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>