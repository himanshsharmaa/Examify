<?php include 'includes/header.php'; ?>

<!-- Main Landing Page Content -->
<main class="bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] text-white">
    <!-- Hero Section -->
    <section id="home" class="min-h-[100vh] flex flex-col lg:flex-row px-4 lg:px-40 items-center justify-center relative">
        <!-- Background Effect -->
        <div class="absolute inset-0 z-0 bg-hero-pattern bg-cover bg-center opacity-30"></div>
        <div class="z-10 flex flex-col lg:flex-row items-center justify-between max-w-7xl w-full">
            <!-- Content aligned to the left -->
            <div class="flex-1 text-center lg:text-left lg:mr-6 mb-6 lg:mb-0">
                <h1 class="text-4xl lg:text-6xl font-extrabold mb-4 lg:mb-8 leading-tight tracking-wide">
                    Welcome to <span class="text-[#d4af37]">Examify</span>
                </h1>
                <p class="text-lg lg:text-xl max-w-xl mx-auto lg:mx-0 mb-4 lg:mb-8 leading-relaxed text-[#c9d1d9]">
                    Elevate your learning experience with our premium online exam platform. Engage, excel, and achieve like never before.
                </p>
                <a href="#about" class="bg-[#d4af37] hover:bg-[#ffcc00] duration-300 text-white p-3 rounded-lg shadow-lg transition">
                    Learn More
                </a>
            </div>

            <!-- Image aligned to the right -->
            <div class="flex-1 text-center lg:text-right lg:ml-8">
                <img src="../../assets/img/home.png" alt="Exam Image" class="max-w-full lg:max-w-xl mb-6 rounded-lg transition-shadow duration-300 object-contain">
            </div>
        </div>
    </section>

    <!-- About and Features Section -->
    <section id="about" class="bg-transparent min-h-[100vh] flex flex-col px-4 lg:px-40 items-center justify-center py-12">
        <div class="container max-w-5xl">
            <h2 class="text-4xl font-extrabold text-center mb-8 text-[#d4af37]">About Our Platform</h2>
            <p class="text-lg leading-relaxed mb-4 text-[#c9d1d9] text-center">
                Experience a cutting-edge platform designed for seamless online assessments with an emphasis on security, performance, and interactivity.
            </p>
            <p class="text-lg leading-relaxed text-[#c9d1d9] text-center mb-12">
                Unlock the potential of technology to simplify and enhance the way exams are conducted.
            </p>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Feature 1 -->
                <div class="bg-[#21262d] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-laptop-code text-4xl text-[#ffcc00]"></i>
                        <h3 class="text-xl font-semibold ml-4 text-[#c9d1d9]">Interactive Interface</h3>
                    </div>
                    <p class="text-[#8b949e]">Seamlessly designed for an engaging and user-friendly exam-taking experience.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-[#21262d] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-shield-alt text-4xl text-[#ffcc00]"></i>
                        <h3 class="text-xl font-semibold ml-4 text-[#c9d1d9]">Advanced Security</h3>
                    </div>
                    <p class="text-[#8b949e]">End-to-end encryption ensures the safety of your data and exam results.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-[#21262d] p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-clock text-4xl text-[#ffcc00]"></i>
                        <h3 class="text-xl font-semibold ml-4 text-[#c9d1d9]">Time Optimization</h3>
                    </div>
                    <p class="text-[#8b949e]">Built-in tools for efficient time management during exams.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="min-h-[100vh] flex flex-col px-4 lg:px-40 items-center justify-center py-12">
        <div class="container max-w-5xl bg-[#21262d] rounded-lg shadow-xl p-8">
            <h2 class="text-4xl font-extrabold text-center mb-8 text-[#d4af37]">Contact Us</h2>
            <form action="contactSubmit.php" method="post" class="grid grid-cols-1 gap-6">
                <input type="text" name="name" placeholder="Your Name" class="p-4 bg-[#30363d] text-white border border-[#30363d] rounded-lg focus:border-[#238636] focus:ring-[#238636]">
                <input type="email" name="email" placeholder="Your Email" class="p-4 bg-[#30363d] text-white border border-[#30363d] rounded-lg focus:border-[#238636] focus:ring-[#238636]">
                <textarea name="message" placeholder="Your Message" rows="5" class="p-4 bg-[#30363d] text-white border border-[#30363d] rounded-lg focus:border-[#238636] focus:ring-[#238636]"></textarea>
                <button type="submit" class="bg-[#d4af37] hover:bg-[#ffcc00] duration-300 text-white p-3 rounded-lg shadow-lg transition">
                    Send Message
                </button>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<!-- Additional Styles -->
<style>
    html {
        scroll-behavior: smooth;
    }

    .bg-hero-pattern {
        background-image: url('image/hero-pattern.png');
    }
</style>
