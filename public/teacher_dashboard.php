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

// Get the teacher's ID
$teacher_id = $_SESSION['user_id'];

// Fetch classes the teacher is managing
$query = "
    SELECT id, class_name, class_description, class_code
    FROM classes
    WHERE teacher_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $teacher_id);
$stmt->execute();
$classes = $stmt->get_result();

// Fetch upcoming exams created by the teacher
$query = "
    SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
    FROM exams
    JOIN classes ON exams.class_id = classes.id
    WHERE classes.teacher_id = ? AND exams.exam_date >= CURDATE()
    ORDER BY exams.exam_date ASC
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $teacher_id);
$stmt->execute();
$exams = $stmt->get_result();

// Handle form submission for creating a new test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_test'])) {
    $exam_name = trim($_POST['exam_name']);
    $exam_date = trim($_POST['exam_date']);
    $class_id = intval($_POST['class_id']);

    // Validate input
    if (empty($exam_name) || empty($exam_date) || empty($class_id)) {
        $error = "All fields are required.";
    } else {
        // Insert new test into the database
        $query = "INSERT INTO exams (exam_name, exam_date, class_id) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ssd', $exam_name, $exam_date, $class_id);

        if ($stmt->execute()) {
            $success = "Test created successfully.";
            // Refresh the list of upcoming exams
            $query = "
                SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
                FROM exams
                JOIN classes ON exams.class_id = classes.id
                WHERE classes.teacher_id = ? AND exams.exam_date >= CURDATE()
                ORDER BY exams.exam_date ASC
            ";
            $stmt = $db->prepare($query);
            $stmt->bind_param('d', $teacher_id);
            $stmt->execute();
            $exams = $stmt->get_result();
        } else {
            $error = "Failed to create test. Please try again.";
        }
    }
}

// Function to generate a unique class code
function generateClassCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white min-h-[100vh] flex flex-col">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Teacher Dashboard</h1>

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
                    <a href="manage_class.php?id=<?php echo $class['id']; ?>" class="text-[#d4af37] hover:underline ml-4">Manage</a>
                </li>
            <?php endwhile; ?>
        </ul>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Create a New Class</h2>
        <form action="teacher_dashboard.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="class_name" class="block text-lg font-medium mb-2">Class Name</label>
                <input type="text" id="class_name" name="class_name" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="class_description" class="block text-lg font-medium mb-2">Class Description</label>
                <textarea id="class_description" name="class_description" class="w-full p-3 rounded bg-gray-800 text-white" rows="4"></textarea>
            </div>
            <button type="submit" name="create_class" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Create Class</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Create a New Test</h2>
        <form action="teacher_dashboard.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="exam_name" class="block text-lg font-medium mb-2">Test Name</label>
                <input type="text" id="exam_name" name="exam_name" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="exam_date" class="block text-lg font-medium mb-2">Test Date</label>
                <input type="date" id="exam_date" name="exam_date" class="w-full p-3 rounded bg-gray-800 text-white" required>
            </div>
            <div class="mb-4">
                <label for="class_id" class="block text-lg font-medium mb-2">Class</label>
                <select id="class_id" name="class_id" class="w-full p-3 rounded bg-gray-800 text-white" required>
                    <option value="">Select Class</option>
                    <?php
                    // Reset the classes result set and fetch again for the dropdown
                    $classes->data_seek(0);
                    while ($class = $classes->fetch_assoc()): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="create_test" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Create Test</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Upcoming Exams</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg">
            <?php while ($exam = $exams->fetch_assoc()): ?>
                <li class="mb-2">
                    <span class="font-bold"><?php echo htmlspecialchars($exam['exam_name']); ?></span>
                    <span class="text-gray-400">Class: <?php echo htmlspecialchars($exam['class_name']); ?></span>
                    <span class="text-gray-400">Date: <?php echo htmlspecialchars($exam['exam_date']); ?></span>
                    <a href="manage_test.php?id=<?php echo $exam['id']; ?>" class="text-[#d4af37] hover:underline ml-4">Manage Test</a>
                    <a href="view_test_history.php?id=<?php echo $exam['id']; ?>" class="text-[#d4af37] hover:underline ml-4">View History</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>