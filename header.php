<!DOCTYPE html>
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
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="../js/main.js"></script>
<link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
<header>
    <div class="container header-content">
        <h1><a href="index.php" class="header-logo">Vultures Capital</a></h1>
        <nav class="main-nav">
            <ul>
                <li><a href="#">Blog</a></li>
                <li><a href="./categories.php">Categories</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">roro</a></li>
            </ul>
        </nav>
        <button id="theme-toggle">Dark Mode</button>
        <div class="social-media">
            <a href="https://twitter.com/VulturesGroup">
                <i class="fa-brands fa-twitter"></i>
            </a>
        </div>
    </div>
</header>
