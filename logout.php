<?php
session_start(); // Start the session

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]);
}

// Destroy the session
session_destroy();

// Redirect to login page or home page
header("Location: homee.html");
exit();
?>
