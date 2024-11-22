
<?php include 'includes/header.php'; ?><?php
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

// Get the student's ID
$student_id = $_SESSION['user_id'];

// Fetch classes the student is enrolled in
$query = "
    SELECT classes.id, classes.class_name, classes.class_description, classes.class_code
    FROM class_students
    JOIN classes ON class_students.class_id = classes.id
    WHERE class_students.student_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $student_id);
$stmt->execute();
$classes = $stmt->get_result();

// Fetch upcoming exams for the student
$query = "
    SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
    FROM exams
    JOIN classes ON exams.class_id = classes.id
    JOIN class_students ON class_students.class_id = classes.id
    WHERE class_students.student_id = ? AND exams.exam_date >= CURDATE()
    ORDER BY exams.exam_date ASC
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $student_id);
$stmt->execute();
$exams = $stmt->get_result();
?>
<body class="font-roboto bg-gray-900 text-white">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Student Dashboard</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Your Classes</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg mb-8">
            <?php while ($class = $classes->fetch_assoc()): ?>
                <li class="mb-2">
                    <span class="font-bold"><?php echo htmlspecialchars($class['class_name']); ?></span>
                    <span class="text-gray-400"><?php echo htmlspecialchars($class['class_description']); ?></span>
                    <span class="text-gray-400">Class Code: <?php echo htmlspecialchars($class['class_code']); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Join a New Class</h2>
        <form action="join_class.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="class_code" class="block text-lg font-medium mb-2">Class Code</label>
                <input type="text" id="class_code" name="class_code" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Join Class</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Upcoming Exams</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg">
            <?php while ($exam = $exams->fetch_assoc()): ?>
                <li class="mb-2">
                    <span class="font-bold"><?php echo htmlspecialchars($exam['exam_name']); ?></span>
                    <span class="text-gray-400">Class: <?php echo htmlspecialchars($exam['class_name']); ?></span>
                    <span class="text-gray-400">Date: <?php echo htmlspecialchars($exam['exam_date']); ?></span>
                    <a href="take_test.php?id=<?php echo $exam['id']; ?>" class="text-[#d4af37] hover:underline ml-4">Take Test</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

<?php include 'includes/footer.php'; ?>
</body>