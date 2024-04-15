<?php include 'dbconfig.php'; ?>
<?php session_start(); // Très important pour utiliser les variables de session
?>
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
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body class="d-flex flex-column min-vh-100 light-mode">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a href="index.php" class="navbar-brand">Vultures Capital</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a href="./articles_choices.php" class="nav-link">Articles</a></li>
                        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
                        <li class="nav-item"><a href="./adminlogin.php" class="nav-link">Admin Login</a></li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li class="nav-item"><a href="./editor.php" class="nav-link">Create an Article</a></li>
                            <li class="nav-item"><a href="./logout.php" class="nav-link">Logout</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Newsletter</a></li>
                    </ul>
                    <button id="theme-toggle" class="btn">
                        <i class="fa fa-moon"></i>
                        <i class="fa fa-sun"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>
<!-- Bootstrap JavaScript à la fin du body pour un chargement de page optimal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="../js/main.js"></script>
<script>
    const themeToggleButton = document.getElementById('theme-toggle');

    themeToggleButton.addEventListener('click', function() {
      document.body.classList.toggle('dark-mode');
      document.body.classList.toggle('light-mode');
    });
</script>
