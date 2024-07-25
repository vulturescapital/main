<?php include 'dbconfig.php'; ?>
<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<head>
    <!-- Les scripts de thème doivent être chargés avant les autres ressources -->
    <script>
        // Votre script pour définir le thème
        (function () {
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
    <title>Vultures Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Votre CSS personnalisé après Bootstrap pour pouvoir écraser les styles si nécessaire -->
    <link rel="stylesheet" href="../css/editor.css">
    <link rel="stylesheet" type="text/css" href="css/editor.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <a href="index_admin.php" class="logo">Vultures</a>
        <ul class="nav-links">
            <li><a href="./index_admin.php">Overview</a></li>
            <li><a href="#">Quickstart</a></li>
            <li>
                <a href="#" class="submenu-toggle">Users <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="./admin_users.php">All Users</a></li>
                    <li><a href="./admin_add_user.php">Add User</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="submenu-toggle">Posts <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="./admin_post.php">All Posts</a></li>
                    <li><a href="./editor.php">Add Article</a></li>
                </ul>
            </li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">Comments</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
        <a href="processes/logout.php" class="logout-btn">Se déconnecter</a>
    </div>
    <button class="toggle-sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                toggleBtn.classList.toggle('collapsed');
            });

            // Submenu toggle
            document.querySelectorAll('.submenu-toggle').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    submenu.classList.toggle('show');
                    this.classList.toggle('expanded');
                });
            });
        });
    </script>