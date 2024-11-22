<?php
// Include database configuration
require_once 'db_config.php';

// Get database connection
$db = getDbConnection();

// Drop existing tables if they exist
$dropTables = "
    DROP TABLE IF EXISTS class_students;
    DROP TABLE IF EXISTS classes;
    DROP TABLE IF EXISTS users;
";

// Create users table
$createUsersTable = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('student', 'teacher') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";

// Create classes table
$createClassesTable = "
    CREATE TABLE classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_name VARCHAR(255) NOT NULL,
        class_description TEXT,
        class_code VARCHAR(10) NOT NULL UNIQUE,
        teacher_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
    );
";

// Create class_students table
$createClassStudentsTable = "
    CREATE TABLE class_students (
        class_id INT NOT NULL,
        student_id INT NOT NULL,
        PRIMARY KEY (class_id, student_id),
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    );
";

// Execute queries
if ($db->multi_query($dropTables)) {
    do {
        // Store first result set
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->next_result());
}

if ($db->query($createUsersTable) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating users table: " . $db->error . "<br>";
}

if ($db->query($createClassesTable) === TRUE) {
    echo "Classes table created successfully.<br>";
} else {
    echo "Error creating classes table: " . $db->error . "<br>";
}

if ($db->query($createClassStudentsTable) === TRUE) {
    echo "Class_Students table created successfully.<br>";
} else {
    echo "Error creating class_students table: " . $db->error . "<br>";
}

// Close the database connection
$db->close();
?>