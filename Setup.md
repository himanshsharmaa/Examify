<h1 align="center">📚 Examify Setup Guide</h1>

<p align="center">
  This guide will help you set up the Examify project on your local machine. Follow the steps below to get started with this online examination and classroom management system.
</p>

------

<h2 align="left">📋 Prerequisites</h2>
<ul>
  <li><strong>PHP</strong>: Ensure you have PHP installed on your machine. You can download it from <a href="https://www.php.net/downloads" target="_blank">here</a>.</li>
  <li><strong>Composer</strong>: A dependency manager for PHP. You can download it from <a href="https://getcomposer.org/download/" target="_blank">here</a>.</li>
  <li><strong>MySQL</strong>: A relational database management system. You can download it from <a href="https://dev.mysql.com/downloads/mysql/" target="_blank">here</a>.</li>
  <li><strong>Web Server</strong>: XAMPP or WAMP for running the project locally. You can download XAMPP from <a href="https://www.apachefriends.org/index.html" target="_blank">here</a> or WAMP from <a href="https://www.wampserver.com/en/" target="_blank">here</a>.</li>
  <li><strong>Git</strong>: A version control system. You can download it from <a href="https://git-scm.com/downloads" target="_blank">here</a>.</li>
</ul>

<h2 align="left">🚀 Installation Steps</h2>
<ol>
  <li><strong>Clone the Repository</strong>:
    <pre><code>git clone https://github.com/himanshsharmaa/Examify.git
cd Examify</code></pre>
  </li>
  <li><strong>Install Dependencies</strong>:
    <pre><code>composer install</code></pre>
  </li>
  <li><strong>Configure the Database</strong>:
    <ul>
      <li>Create a new MySQL database.</li>
      <li>Import the <code>seed.sql</code> file located in the <code>database/</code> directory to set up the initial database structure and data.</li>
      <li>Update the database configuration in <code>config/db_config.php</code> with your database credentials.</li>
    </ul>
  </li>
  <li><strong>Set Up Environment Variables</strong>:
    <ul>
      <li>Rename the <code>env.php.example</code> file in the <code>config/</code> directory to <code>env.php</code>.</li>
      <li>Update the environment variables in <code>env.php</code> with your configuration settings.</li>
    </ul>
  </li>
  <li><strong>Run the Project</strong>:
    <ul>
      <li>Start your web server (e.g., XAMPP, WAMP).</li>
      <li>Place the project directory (Examify) in the web server's root directory (e.g., <code>C:\xampp\htdocs\</code> for XAMPP).</li>
      <li>Access the project in your web browser at <code>http://localhost/Examify/</code>.</li>
    </ul>
  </li>
</ol>

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

<h2 align="left">🤝 Contributing</h2>
<p>
  Contributions are welcome! Please open an issue or submit a pull request.
</p>

<h2 align="left">📬 Contact</h2>
<p align="left">Feel free to reach out via <a href="mailto:talk.himanshsharma@gmail.com">Email</a>.</p>

------

