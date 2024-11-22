<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// Include necessary configuration and functions
require_once '../config/constants.php';
require_once '../config/functions.php';

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Function to send an SSE message
function sendSSEMessage($id, $message) {
    echo "id: $id\n";
    echo "data: $message\n\n";
    ob_flush();
    flush();
}

// Fetch updates from the database
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

while (true) {
    // Fetch new exams or class updates
    if ($user_role === 'teacher') {
        $query = "SELECT * FROM exams WHERE teacher_id = ? AND created_at > NOW() - INTERVAL 1 MINUTE";
    } else {
        $query = "SELECT * FROM exams WHERE class_id IN (SELECT class_id FROM class_students WHERE student_id = ?) AND created_at > NOW() - INTERVAL 1 MINUTE";
    }
    $stmt = $db->prepare($query);
    $stmt->bind_param('d', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $message = json_encode($row);
        sendSSEMessage($row['id'], $message);
    }

    // Sleep for a while before checking for updates again
    sleep(10);
}
?>