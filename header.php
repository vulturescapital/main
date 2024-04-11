<?php include 'dbconfig.php'; ?>
<html lang="en">
<head>
    <!-- Les scripts de thème doivent être chargés avant les autres ressources -->
    <script>
        // Votre script pour définir le thème
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.add('light-mode');
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vultures Blog</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Votre CSS personnalisé après Bootstrap pour pouvoir écraser les styles si nécessaire -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body class="light-mode"> <!-- Ajoutez ici la classe par défaut du thème -->
<header>
    <nav class="navbar navbar-expand-lg navbar-light"> <!-- Retirer bg-light pour contrôler la couleur via le CSS -->
        <div class="container">
            <a href="index.php" class="navbar-brand">Vultures Capital</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="#" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="./categories.php" class="nav-link">Categories</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Newsletter</a></li>
                </ul>
            </div>
            <!-- Bouton pour changer de thème -->
            <button id="theme-toggle" class="btn btn-sm btn-dark">Dark Mode</button>
        </div>
    </nav>
</header>

<!-- Bootstrap JavaScript à la fin du body pour un chargement de page optimal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
