<?php
session_start();
session_unset();
session_destroy();
header("Location: ../admin_login.php"); // Redirige vers la page de login après la déconnexion
$conn = null;
exit;
?>
