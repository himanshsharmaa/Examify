<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the login page or homepage
header('Location: ../../public/index.php');
exit;
?>