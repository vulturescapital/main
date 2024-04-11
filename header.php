<?php
$host = 'vulturescapital.fr'; // Remplacez par l'adresse du serveur fournie par O2switch
$dbname = 'kinu7234_vultures'; // Nom de votre base de données
$user = 'kinu7234_romain'; // Nom d'utilisateur de la base de données
$password = 'Napoleon0304!'; // Mot de passe de la base de données

// DSN pour la connexion PDO à MySQL
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Créez une instance de PDO pour établir une connexion à la BDD
    $pdo = new PDO($dsn, $user, $password);
    // Configurez le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log("Connecté à la base de données $dbname avec succès.");
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
}
?>

<html lang="en">
<head>
    <script>
      // Immediately invoked function to set the theme before the page renders
      (function() {
        // Check local storage for the theme preference
        var theme = localStorage.getItem('theme');

        // Apply the theme class to the body immediately
        if (theme === 'dark') {
          document.body.classList.add('dark-mode');
          // Optionally, you might want to remove the light-mode class if it's set by default in your HTML
          document.body.classList.remove('light-mode');
        } else {
          document.body.classList.add('light-mode');
        }
      })();
    </script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vultures Blog</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3pV8TcC2-xxW6GLSsXK9saPc7PJihxpk964IviEry75+6gjV76o+mS0JX" crossorigin="anonymous">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="../js/main.js"></script>
<link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
<header>
    <div class="container header-content">
        <h1><a href="index.php" class="header-logo">Vultures Capital</a></h1>
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a href="#" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="./categories.php" class="nav-link">Categories</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Newsletter</a></li>
                </ul>
            </div>
        </nav>
        <button id="theme-toggle" class="btn btn-dark">Dark Mode</button>
        <div class="social-media">
            <a href="https://twitter.com/VulturesGroup">
                <i class="fa-brands fa-twitter"></i>
            </a>
        </div>
    </div>
</header>
