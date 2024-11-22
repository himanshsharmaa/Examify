<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examify - Online Exam Platform</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom styles for dropdown */
        #cta-dropdown-menu {
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        #cta-dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="font-roboto bg-gray-900 text-white">

    <!-- Sticky Navigation Bar -->
    <header class="w-full bg-[#0f2027] shadow-lg fixed top-0 left-0 z-50">
        <nav class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <a href="index.php" class="text-4xl font-bold text-[#d4af37] hover:text-[#ffcc00] transition-colors duration-300">Examify</a>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button id="navbar-toggle" class="text-white hover:text-[#d4af37]" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Navbar Links for Large Screens -->
            <div class="hidden lg:flex space-x-8">
                <a href="index.php/#home" class="hover:text-[#d4af37] hover:underline transition duration-300">Home</a>
                <a href="index.php/#about" class="hover:text-[#d4af37] hover:underline transition duration-300">About</a>
                <a href="index.php/#contact" class="hover:text-[#d4af37] hover:underline transition duration-300">Contact</a>

                <!-- CTA Button with Dropdown -->
                <div class="relative" id="cta-dropdown">
                    <button id="dropdown-toggle" class="text-white hover:text-[#d4af37] flex items-center space-x-2" aria-haspopup="true">
                        <span><i class="fas fa-user"></i></span>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="cta-dropdown-menu" class="absolute right-0 bg-[#1a1a2e] text-white rounded-lg shadow-lg mt-2 w-48">
                        <ul class="py-2">
                            <!-- Check if the user is logged in -->
                            <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                                <!-- Check user role for teacher-specific links -->
                                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher'): ?>
                                    <li><a href="teacher_dashboard.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Class</a></li>
                                    <li><a href="teacher_profile.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Profile</a></li>
                                <!-- Check user role for student-specific links -->
                                <?php elseif(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student'): ?>
                                    <li><a href="student_dashboard.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Class</a></li>
                                    <li><a href="student_profile.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Profile</a></li>
                                <?php endif; ?>
                                <!-- Logout link for logged-in users -->
                                <li><a href="../src/controllers/logout.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Logout</a></li>
                            <?php else: ?>
                                <!-- Links for non-logged-in users -->
                                <li><a href="./create_class.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Create Class</a></li>
                                <li><a href="./join_class.php" class="block px-4 py-2 hover:bg-[#d4af37] hover:text-white transition duration-300">Join Class</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Script for Menu and Dropdown Toggling -->
    <script>
        // Mobile menu toggle
        document.getElementById("navbar-toggle").addEventListener("click", function() {
            const menu = document.getElementById("navbar-menu");
            menu.classList.toggle("hidden");
        });

        // Dropdown toggle on click
        const dropdownToggle = document.getElementById("dropdown-toggle");
        const dropdownMenu = document.getElementById("cta-dropdown-menu");

        dropdownToggle.addEventListener("click", function () {
            dropdownMenu.classList.toggle("show");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function (event) {
            if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove("show");
            }
        });
    </script>
</body>
</html>