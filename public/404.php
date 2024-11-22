<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Examify</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-roboto bg-gray-900 text-white">
    <?php include 'includes/header.php'; ?>

    <div class="flex items-center justify-center h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-[#d4af37] mb-4">404</h1>
            <p class="text-2xl mb-8">Oops! The page you are looking for does not exist.</p>
            <a href="../../index.php" class="bg-[#d4af37] text-white py-3 px-6 rounded hover:bg-[#ffcc00] transition duration-300">Go to Homepage</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>