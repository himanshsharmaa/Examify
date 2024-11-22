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

// Get the teacher's ID
$teacher_id = $_SESSION['user_id'];

// Fetch classes the teacher is managing
$query = "
    SELECT id, class_name
    FROM classes
    WHERE teacher_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $teacher_id);
$stmt->execute();
$classes = $stmt->get_result();

// Fetch results for a specific class if class_id is provided
$results = [];
if (isset($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);

    $query = "
        SELECT students.name AS student_name, exams.exam_name, results.score
        FROM results
        JOIN students ON results.student_id = students.id
        JOIN exams ON results.exam_id = exams.id
        WHERE exams.class_id = ?
    ";
    $stmt = $db->prepare($query);
    $stmt->bind_param('d', $class_id);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results - Examify</title>
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
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">View Results</h1>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Select a Class</h2>
        <form action="get_results.php" method="GET" class="mb-8">
            <select name="class_id" class="w-full p-3 rounded bg-gray-800 text-white mb-4" required>
                <option value="">Select Class</option>
                <?php while ($class = $classes->fetch_assoc()): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">View Results</button>
        </form>

        <?php if (!empty($results)): ?>
            <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Results</h2>
            <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg">
                <?php while ($result = $results->fetch_assoc()): ?>
                    <li class="mb-2">
                        <span class="font-bold"><?php echo htmlspecialchars($result['student_name']); ?></span>
                        <span class="text-gray-400"> - <?php echo htmlspecialchars($result['exam_name']); ?>: </span>
                        <span class="text-gray-400"><?php echo htmlspecialchars($result['score']); ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>