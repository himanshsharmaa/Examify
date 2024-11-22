-- Drop existing tables if they exist
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS exams;
DROP TABLE IF EXISTS class_students;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(255) NOT NULL,
    class_description TEXT,
    class_code VARCHAR(10) NOT NULL UNIQUE,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create class_students table
CREATE TABLE class_students (
    class_id INT NOT NULL,
    student_id INT NOT NULL,
    PRIMARY KEY (class_id, student_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create exams table
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    class_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Create questions table
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    exam_id INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Create answers table
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_id INT NOT NULL,
    student_id INT NOT NULL,
    answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create results table
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    score INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert initial users
INSERT INTO users (name, email, password, role) VALUES
('Teacher1', 'teacher1@email.com', '$2y$10$e0MYzXyjpJS2JxFQ7z2EhuJ7o1q0G6r5r5r5r5r5r5r5r5r5r5r5', 'teacher'),
('Teacher2', 'teacher2@email.com', '$2y$10$e0MYzXyjpJS2JxFQ7z2EhuJ7o1q0G6r5r5r5r5r5r5r5r5r5r5', 'teacher'),
('Student1', 'student1@email.com', '$2y$10$e0MYzXyjpJS2JxFQ7z2EhuJ7o1q0G6r5r5r5r5r5r5r5r5r5r5', 'student'),
('Student2', 'student2@email.com', '$2y$10$e0MYzXyjpJS2JxFQ7z2EhuJ7o1q0G6r5r5r5r5r5r5r5r5r5r5', 'student');

-- Insert initial classes
INSERT INTO classes (class_name, class_description, class_code, teacher_id) VALUES
('Math 101', 'Basic Mathematics', 'ABC123', 1),
('Science 101', 'Basic Science', 'DEF456', 2);

-- Enroll students in classes
INSERT INTO class_students (class_id, student_id) VALUES
(1, 3),
(1, 4),
(2, 3);

-- Insert initial exams
INSERT INTO exams (exam_name, exam_date, class_id) VALUES
('Math Exam 1', '2023-12-01', 1),
('Science Exam 1', '2023-12-02', 2);

-- Insert initial questions for Math Exam 1
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, exam_id) VALUES
('What is 2 + 2?', '3', '4', '5', '6', 'B', 1),
('What is 3 + 5?', '7', '8', '9', '10', 'B', 1);

-- Insert initial questions for Science Exam 1
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, exam_id) VALUES
('What is H2O?', 'Water', 'Oxygen', 'Hydrogen', 'Carbon Dioxide', 'A', 2),
('What is CO2?', 'Water', 'Oxygen', 'Hydrogen', 'Carbon Dioxide', 'D', 2);