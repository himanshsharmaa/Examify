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

// Get the class ID from the URL
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch class details from the database
$query = "SELECT * FROM classes WHERE id = ? AND teacher_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('dd', $class_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: teacher_dashboard.php');
    exit;
}

$class = $result->fetch_assoc();

// Fetch students enrolled in the class
$query = "
    SELECT users.id, users.name, users.email
    FROM class_students
    JOIN users ON class_students.student_id = users.id
    WHERE class_students.class_id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $class_id);
$stmt->execute();
$students = $stmt->get_result();

// Handle form submission for editing class details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_class'])) {
    $class_name = trim($_POST['class_name']);
    $class_description = trim($_POST['class_description']);

    // Validate input
    if (empty($class_name)) {
        $error = "Class name is required.";
    } else {
        // Update class details in the database
        $query = "UPDATE classes SET class_name = ?, class_description = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ssd', $class_name, $class_description, $class_id);

        if ($stmt->execute()) {
            $success = "Class updated successfully.";
            // Refresh class details
            $query = "SELECT * FROM classes WHERE id = ? AND teacher_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('dd', $class_id, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $class = $result->fetch_assoc();
        } else {
            $error = "Failed to update class. Please try again.";
        }
    }
}

// Handle form submission for removing a student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_student'])) {
    $student_id = intval($_POST['student_id']);

    // Remove student from the class
    $query = "DELETE FROM class_students WHERE class_id = ? AND student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dd', $class_id, $student_id);

    if ($stmt->execute()) {
        $success = "Student removed successfully.";
        // Refresh students list
        $query = "
            SELECT users.id, users.name, users.email
            FROM class_students
            JOIN users ON class_students.student_id = users.id
            WHERE class_students.class_id = ?
        ";
        $stmt = $db->prepare($query);
        $stmt->bind_param('d', $class_id);
        $stmt->execute();
        $students = $stmt->get_result();
    } else {
        $error = "Failed to remove student. Please try again.";
    }
}

// Handle form submission for deleting the class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    // Delete the class from the database
    $query = "DELETE FROM classes WHERE id = ? AND teacher_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('dd', $class_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header('Location: teacher_dashboard.php');
        exit;
    } else {
        $error = "Failed to delete class. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white">

    <div class="max-w-7xl mx-auto py-12 px-6">
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Manage Class</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="manage_class.php?id=<?php echo $class_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="class_name" class="block text-lg font-medium mb-2">Class Name</label>
                <input type="text" id="class_name" name="class_name" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="class_description" class="block text-lg font-medium mb-2">Class Description</label>
                <textarea id="class_description" name="class_description" class="w-full p-3 rounded bg-gray-800 text-white" rows="4"><?php echo htmlspecialchars($class['class_description']); ?></textarea>
            </div>
            <button type="submit" name="edit_class" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Update Class</button>
        </form>

        <h2 class="text-2xl font-bold text-[#d4af37] mb-4">Enrolled Students</h2>
        <ul class="bg-[#1a1a2e] p-4 rounded-lg shadow-lg mb-8">
            <?php while ($student = $students->fetch_assoc()): ?>
                <li class="mb-2 flex justify-between items-center">
                    <div>
                        <span class="font-bold"><?php echo htmlspecialchars($student['name']); ?></span>
                        <span class="text-gray-400"><?php echo htmlspecialchars($student['email']); ?></span>
                    </div>
                    <form action="manage_class.php?id=<?php echo $class_id; ?>" method="POST">
                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                        <button type="submit" name="remove_student" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-700 transition duration-300">Remove</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>

        <form action="manage_class.php?id=<?php echo $class_id; ?>" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <button type="submit" name="delete_class" class="bg-red-500 text-white py-3 px-6 rounded hover:bg-red-700 transition duration-300">Delete Class</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>