<h1 align="center">📚 Examify</h1>

<p align="center">
  Examify is an online examination and classroom management system designed for educational institutions. Built to emulate key features of Google Classroom, it offers seamless login, class joining via code, exam management, result processing, and user roles for students, teachers, and administrators. The project is crafted with a modern tech stack, featuring PHP, MySQL, JavaScript, and Tailwind CSS, ensuring a polished and interactive UI enhanced by Animate.css.
</p>

<div align="center">
    <img src="https://github.com/himanshsharmaa/Examify/blob/main/assets/img/Examify.png?raw=true" alt=" Header" />
</div>

------

<p align="center">
  <img src="https://readme-typing-svg.herokuapp.com?font=Fira+Code&size=22&pause=1000&color=4CAF50&center=true&vCenter=true&width=435&lines=Manage+Exams+Efficiently;Student+and+Teacher+Focused;Secure+and+User-Friendly" alt="Typing SVG">
</p>

<h2 align="left">🌟 Features</h2>
<ul>
  <li><strong>Secure Authentication</strong>: Role-based login for students, teachers, and admins.</li>
  <li><strong>Join Classes with Code</strong>: Students can join with a unique class code.</li>
  <li><strong>Comprehensive Dashboard</strong>: Personalized views for students, teachers, and admins.</li>
  <li><strong>Exam Management</strong>: Create, manage, and monitor exams with ease.</li>
  <li><strong>Interactive UI</strong>: Modern, responsive design using Tailwind CSS and Animate.css.</li>
  <li><strong>Result Processing</strong>: Efficient result viewing and management for teachers and students.</li>
</ul>

<h2 align="left">🔧 Tech Stack</h2>
<p align="left">
  <a href="https://www.html.com/" target="_blank"><img src="https://img.shields.io/badge/HTML-E34C26?style=for-the-badge&logo=html5&logoColor=white"/></a>
  <a href="https://www.php.net/" target="_blank"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"/></a>
  <a href="https://www.mysql.com/" target="_blank"><img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/></a>
  <a href="https://getbootstrap.com/" target="_blank"><img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white"/></a>
  <a href="https://tailwindcss.com/" target="_blank"><img src="https://img.shields.io/badge/Tailwind_CSS-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white"/></a>
  <a href="https://github.com/" target="_blank"><img src="https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white"/></a>
</p>

<h2 align="left">📂 Project Structure</h2>
<pre>
Examify/
│   .htaccess
│
├───assets
│   ├───css
│   ├───fonts
│   │       Roboto-Bold.tff      
│   │       Roboto-Regular.tff   
│   │       
│   ├───img
│   │   │   home.png
│   │   │   
│   │   ├───banners
│   │   └───icons
│   │           settings_icon.png
│   │           user_icon.png
│   │
│   └───js
│
├───config
│       constants.php
│       cors.php
│       env.php
│       functions.php
│       routes.php
│
├───database
│       db_config.php
│       migrate.php
│       seed.sql
│
├───docs
│       API.md
│       DB_STRUCTURE.md
│       LICENSE
│       README.md
│       SETUP.md
│
├───logs
│       access.log
│       error.log
│       websocket.log
│
├───public
│   │   404.php
│   │   class.php
│   │   create_class.php
│   │   index.php
│   │   join_class.php
│   │   login.php
│   │   manage_class.php
│   │   manage_test.php
│   │   result.php
│   │   select_role.php
│   │   signup.php
│   │   student_dashboard.php
│   │   student_profile.php
│   │   take_test.php
│   │   teacher_dashboard.php
│   │   teacher_profile.php
│   │   test.php
│   │   view_test_history.php
│   │
│   └───includes
│           footer.php
│           header.php
│
├───src
│   │   api.php
│   │   ws_server.php
│   │
│   └───controllers
│           auth.php
│           create_class.php
│           create_test.php
│           dashboard.php
│           get_results.php
│           join_class.php
│           login.php
│           logout.php
│           manage_class.php
│           profile.php
│           realtime_updates.php
│           register.php
│           submit_test.php
│
├───tests
│       auth_test.php
│       class_test.php
│       realtime_test.php
│       test_submission_test.php
│       utils_test.php
│
└───uploads
    ├───profile_pics
    │       student1.jpg
    │       student2.jpg
    │       teacher1.jpg
    │
    ├───reports
    │       report1.pdf
    │       report2.pdf
    │       report3.pdf
    │
    └───test_files
            answer_key.txt
            test1.pdf
            test2.docx
</pre>

<h2 align="left">🚀 Installation</h2>
<ol>
  <li>Clone the repository:
    <pre><code>git clone https://github.com/ihimanshsharma/Examify.git
cd Examify</code></pre>
  </li>
  <li>Install dependencies:
    <pre><code>composer install</code></pre>
  </li>
  <li>Configure the database in <code>config/database.php</code>.</li>
</ol>

<h2 align="left">💡 Usage</h2>
<p>
  Run the project on a local server (e.g., XAMPP, WAMP) and access it through <code>http://localhost/Examify/</code>.
</p>

<h2 align="left">📁 File Descriptions</h2>
<ul>
  <li><code>assets/</code>: Contains CSS, fonts, images, and JavaScript files.</li>
  <li><code>config/</code>: Configuration files for constants, CORS, environment variables, functions, and routes.</li>
  <li><code>database/</code>: Database configuration, migration, and seed files.</li>
  <li><code>docs/</code>: Documentation files including API, database structure, license, and setup instructions.</li>
  <li><code>logs/</code>: Log files for access, errors, and websocket activities.</li>
  <li><code>public/</code>: Publicly accessible files including PHP scripts for various functionalities and includes for header and footer.</li>
  <li><code>src/</code>: Source files including API, websocket server, and controllers for various functionalities.</li>
  <li><code>tests/</code>: Test files for authentication, class management, real-time updates, test submission, and utility functions.</li>
  <li><code>uploads/</code>: Uploaded files including profile pictures, reports, and test files.</li>
</ul>

<h2 align="left">🔮 Future Enhancements</h2>
<ul>
  <li><strong>Real-time Notifications</strong>: Implement real-time notifications for exam updates and results.</li>
  <li><strong>Advanced Analytics</strong>: Provide detailed analytics and reports for teachers and administrators.</li>
  <li><strong>Mobile App</strong>: Develop a mobile application for easier access and management.</li>
  <li><strong>Multi-language Support</strong>: Add support for multiple languages to cater to a diverse user base.</li>
  <li><strong>Integration with LMS</strong>: Integrate with popular Learning Management Systems (LMS) for seamless data exchange.</li>
</ul>

<h2 align="left">📝 License</h2>
<p align="left">This project is licensed under the <strong>MIT License</strong>.</p>

<h2 align="left">🤝 Contributing</h2>
<p>
  Contributions are welcome! Please open an issue or submit a pull request.
</p>

<h2 align="left">📬 Contact</h2>
<p align="left">Feel free to reach out via <a href="mailto:talk.himanshsharma@gmail.com">Email</a>.</p>

------

Made with ❤️ by <a href="https://github.com/keplor-io">Keplor.Io</a>
