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

// Get the user's ID
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('d', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: 404.php');
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_hash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $user['password'];

    // Validate input
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } else {
        // Update user details in the database
        $query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sssd', $name, $email, $password_hash, $user_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            $user['name'] = $name;
            $user['email'] = $email;
            $user['password'] = $password_hash;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Examify</title>
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
        <h1 class="text-4xl font-bold text-[#d4af37] mb-8">Profile</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="profile.php" method="POST" class="bg-[#1a1a2e] p-8 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="name" class="block text-lg font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-lg font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full p-3 rounded bg-gray-800 text-white" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-lg font-medium mb-2">Password (leave blank to keep current password)</label>
                <input type="password" id="password" name="password" class="w-full p-3 rounded bg-gray-800 text-white">
            </div>
            <button type="submit" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Update Profile</button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>