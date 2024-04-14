<?php
session_start();
session_unset();
session_destroy();
header("Location: adminlogin.php"); // Redirige vers la page de login après la déconnexion
exit;
?>
