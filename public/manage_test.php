<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    SELECT exams.id, exams.exam_name, exams.exam_date, exams.time_limit, exams.start_time, classes.class_name
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
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $query = "INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dssssss', $test_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

    if ($stmt->execute()) {
        // Redirect to the same page to prevent form resubmission
        header('Location: manage_test.php?id=' . $test_id);
        exit;
    } else {
        $error = "Failed to add question. Please try again.";
    }
}

// Handle form submission for updating test details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_test'])) {
    $time_limit = intval($_POST['time_limit']);
    $start_time = $_POST['start_time'];

    $query = "UPDATE exams SET time_limit = ?, start_time = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dsi', $time_limit, $start_time, $test_id);

    if ($stmt->execute()) {
        // Redirect to the same page to prevent form resubmission
        header('Location: manage_test.php?id=' . $test_id);
        exit;
    } else {
        $error = "Failed to update test details. Please try again.";
    }
}

include 'includes/header.php';
?>

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

    <!-- Display existing questions -->
    <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Questions</h2>
    <?php while ($question = $questions->fetch_assoc()): ?>
        <div class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg mb-4">
            <p class="text-lg font-medium mb-2"><?php echo htmlspecialchars($question['question_text']); ?></p>
            <ul class="list-disc list-inside">
                <li>A: <?php echo htmlspecialchars($question['option_a']); ?></li>
                <li>B: <?php echo htmlspecialchars($question['option_b']); ?></li>
                <li>C: <?php echo htmlspecialchars($question['option_c']); ?></li>
                <li>D: <?php echo htmlspecialchars($question['option_d']); ?></li>
            </ul>
            <p class="mt-2">Correct Option: <?php echo htmlspecialchars($question['correct_option']); ?></p>
        </div>
    <?php endwhile; ?>

    <!-- Form to add a new question -->
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

    <!-- Form to update test details -->
    <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Set Test Details</h2>
    <form action="manage_test.php?id=<?php echo $test_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
        <div class="mb-4">
            <label for="time_limit" class="block text-lg font-medium mb-2">Time Limit (minutes)</label>
            <input type="number" id="time_limit" name="time_limit" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($test['time_limit']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="start_time" class="block text-lg font-medium mb-2">Start Time</label>
            <input type="datetime-local" id="start_time" name="start_time" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($test['start_time']); ?>" required>
        </div>
        <button type="submit" name="update_test" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Set Test</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>