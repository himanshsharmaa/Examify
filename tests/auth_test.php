<?php
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Include necessary configuration and functions
        require_once '../config/constants.php';
        require_once '../config/functions.php';

        // Create a mock database connection
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Ensure the database is in a known state
        $this->db->query("DELETE FROM users");
        $this->db->query("INSERT INTO users (name, email, password, role) VALUES ('Test Teacher', 'teacher@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 'teacher')");
        $this->db->query("INSERT INTO users (name, email, password, role) VALUES ('Test Student', 'student@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 'student')");
    }

    protected function tearDown(): void
    {
        // Close the database connection
        $this->db->close();
    }

    public function testLoginTeacher()
    {
        $_POST['email'] = 'teacher@example.com';
        $_POST['password'] = 'password';
        $_POST['login'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Location: teacher_dashboard.php', $output);
    }

    public function testLoginStudent()
    {
        $_POST['email'] = 'student@example.com';
        $_POST['password'] = 'password';
        $_POST['login'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Location: student_dashboard.php', $output);
    }

    public function testLoginInvalidCredentials()
    {
        $_POST['email'] = 'invalid@example.com';
        $_POST['password'] = 'invalid';
        $_POST['login'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Invalid email or password.', $output);
    }

    public function testRegisterTeacher()
    {
        $_POST['name'] = 'New Teacher';
        $_POST['email'] = 'newteacher@example.com';
        $_POST['password'] = 'newpassword';
        $_POST['role'] = 'teacher';
        $_POST['register'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Registration successful. You can now log in.', $output);

        $result = $this->db->query("SELECT * FROM users WHERE email = 'newteacher@example.com'");
        $this->assertEquals(1, $result->num_rows);
    }

    public function testRegisterStudent()
    {
        $_POST['name'] = 'New Student';
        $_POST['email'] = 'newstudent@example.com';
        $_POST['password'] = 'newpassword';
        $_POST['role'] = 'student';
        $_POST['register'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Registration successful. You can now log in.', $output);

        $result = $this->db->query("SELECT * FROM users WHERE email = 'newstudent@example.com'");
        $this->assertEquals(1, $result->num_rows);
    }

    public function testRegisterDuplicateEmail()
    {
        $_POST['name'] = 'Duplicate User';
        $_POST['email'] = 'teacher@example.com';
        $_POST['password'] = 'password';
        $_POST['role'] = 'teacher';
        $_POST['register'] = true;

        ob_start();
        include '../auth.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Email is already registered.', $output);
    }
}
?>