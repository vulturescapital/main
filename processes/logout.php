<?php
define('SECURE_ACCESS', true);

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session.
session_destroy();

// Regenerate the session ID for added security.
// This helps prevent session fixation attacks.
session_regenerate_id(true);

// Redirect to the login page
if (!headers_sent()) {
    header("Location: ../admin_login.php");
    exit;
} else {
    echo '<script>window.location.href = "../admin_login.php";</script>';
    exit;
}
?>
