<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Default username
define('DB_PASS', ''); // No password
define('DB_NAME', 'examify');

// Create a connection to the database
function getDbConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}

// Example usage
$db = getDbConnection();
?>