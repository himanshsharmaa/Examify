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

// Get the test ID from the URL
$test_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch test details from the database
$query = "
    SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
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
    SELECT id, question_text, correct_option
    FROM questions
    WHERE exam_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $test_id);
$stmt->execute();
$questions = $stmt->get_result();

// Handle form submission for test answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'];
    $student_id = $_SESSION['user_id'];
    $score = 0;

    // Calculate the score
    while ($question = $questions->fetch_assoc()) {
        $question_id = $question['id'];
        $correct_option = $question['correct_option'];
        $student_answer = isset($answers[$question_id]) ? $answers[$question_id] : '';

        if ($student_answer === $correct_option) {
            $score++;
        }

        // Save the student's answer to the database
        $query = "INSERT INTO answers (exam_id, question_id, student_id, answer) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ddds', $test_id, $question_id, $student_id, $student_answer);
        $stmt->execute();
    }

    // Save the student's score to the database
    $query = "INSERT INTO results (exam_id, student_id, score) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ddd', $test_id, $student_id, $score);
    $stmt->execute();

    $success = "Test submitted successfully. Your score is $score.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($test['exam_name']); ?> - Examify</title>
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
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8"><?php echo htmlspecialchars($test['exam_name']); ?></h1>
        <p class="text-lg mb-8">Class: <?php echo htmlspecialchars($test['class_name']); ?></p>
        <p class="text-lg mb-8">Date: <?php echo htmlspecialchars($test['exam_date']); ?></p>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="submit_test.php?id=<?php echo $test_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <?php while ($question = $questions->fetch_assoc()): ?>
                <div class="mb-4">
                    <p class="text-lg font-medium mb-2"><?php echo htmlspecialchars($question['question_text']); ?></p>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_a" name="answers[<?php echo $question['id']; ?>]" value="A" required>
                        <label for="question_<?php echo $question['id']; ?>_a">A</label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_b" name="answers[<?php echo $question['id']; ?>]" value="B" required>
                        <label for="question_<?php echo $question['id']; ?>_b">B</label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_c" name="answers[<?php echo $question['id']; ?>]" value="C" required>
                        <label for="question_<?php echo $question['id']; ?>_c">C</label>
                    </div>
                    <div class="mb-2">
                        <input type="radio" id="question_<?php echo $question['id']; ?>_d" name="answers[<?php echo $question['id']; ?>]" value="D" required>
                        <label for="question_<?php echo $question['id']; ?>_d">D</label>
                    </div>
                </div>
            <?php endwhile; ?>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Submit Test</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>