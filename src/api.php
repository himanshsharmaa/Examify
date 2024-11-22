<?php
session_start();

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the request method and endpoint
$request_method = $_SERVER['REQUEST_METHOD'];
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// Handle different API endpoints
switch ($endpoint) {
    case 'user':
        handleUserEndpoint($request_method);
        break;
    case 'classes':
        handleClassesEndpoint($request_method);
        break;
    case 'exams':
        handleExamsEndpoint($request_method);
        break;
    case 'results':
        handleResultsEndpoint($request_method);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Function to handle user endpoint
function handleUserEndpoint($method) {
    global $db;
    $user_id = $_SESSION['user_id'];

    if ($method === 'GET') {
        $query = "SELECT id, name, email, role FROM users WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('d', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
}

// Function to handle classes endpoint
function handleClassesEndpoint($method) {
    global $db;
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];

    if ($method === 'GET') {
        if ($user_role === 'teacher') {
            $query = "SELECT id, class_name, class_description, class_code FROM classes WHERE teacher_id = ?";
        } else {
            $query = "SELECT classes.id, classes.class_name, classes.class_description, classes.class_code
                      FROM class_students
                      JOIN classes ON class_students.class_id = classes.id
                      WHERE class_students.student_id = ?";
        }
        $stmt = $db->prepare($query);
        $stmt->bind_param('d', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($classes);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
}

// Function to handle exams endpoint
function handleExamsEndpoint($method) {
    global $db;
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];

    if ($method === 'GET') {
        if ($user_role === 'teacher') {
            $query = "SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
                      FROM exams
                      JOIN classes ON exams.class_id = classes.id
                      WHERE classes.teacher_id = ?";
        } else {
            $query = "SELECT exams.id, exams.exam_name, exams.exam_date, classes.class_name
                      FROM exams
                      JOIN classes ON exams.class_id = classes.id
                      JOIN class_students ON class_students.class_id = classes.id
                      WHERE class_students.student_id = ?";
        }
        $stmt = $db->prepare($query);
        $stmt->bind_param('d', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exams = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($exams);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
}

// Function to handle results endpoint
function handleResultsEndpoint($method) {
    global $db;
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];

    if ($method === 'GET') {
        if ($user_role === 'teacher') {
            $query = "SELECT students.name AS student_name, exams.exam_name, results.score
                      FROM results
                      JOIN students ON results.student_id = students.id
                      JOIN exams ON results.exam_id = exams.id
                      WHERE exams.class_id IN (SELECT id FROM classes WHERE teacher_id = ?)";
        } else {
            $query = "SELECT exams.exam_name, results.score
                      FROM results
                      JOIN exams ON results.exam_id = exams.id
                      WHERE results.student_id = ?";
        }
        $stmt = $db->prepare($query);
        $stmt->bind_param('d', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($results);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
}
?>