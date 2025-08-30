<?php
// Start session
session_start();

// Destroy all session data
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear any other authentication cookies (if you have any)
// Example: setcookie('remember_me', '', time() - 3600, '/');

// Redirect to login page or home page
header("Location: index.html"); // Change to your login page
exit();
?>