<?php
// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS') || SECURE_ACCESS !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit('Direct access forbidden.');
}

// Start output buffering
ob_start();

// Include database configuration securely
require_once 'dbconfig.php';

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("HTTP/1.1 403 Forbidden");
        exit('CSRF token validation failed.');
    }
}

// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Disable error display and enable logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); // Make sure this path is correct and writable

// Set a custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr on line $errline in file $errfile");
    return true;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vultures Blog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <style>
        /* Ensure the logo size is consistent */
        .navbar-brand img {
            height: 35px;
            width: auto;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand d-flex align-items-center">
                <img src="../images/logo.png" alt="Vulture Logo">
            </a>
            <div class="d-flex order-lg-3">
                <div class="search-icon mr-3">
                    <a href="javascript:void(0);" class="nav-link" id="search-toggle"><i class="fas fa-search"></i></a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse order-lg-2" id="navbarContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a href="./articles_choices.php" class="nav-link">Articles</a></li>
                    <li class="nav-item"><a href="./contact.php" class="nav-link">Contact</a></li>
                    <li class="nav-item"><a href="./newsletter.php" class="nav-link">Newsletter</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Search Overlay -->
<div id="search-overlay" class="search-overlay d-none">
    <div class="search-overlay-content">
        <button id="close-search" class="close-search">&times;</button>
        <input type="text" id="search-input" class="search-input" placeholder="Search Vultures Blog">
        <div id="search-results" class="search-results"></div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchToggle = document.getElementById('search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const closeSearch = document.getElementById('close-search');
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');

        searchToggle.addEventListener('click', function () {
            searchOverlay.classList.remove('d-none');
            searchInput.focus();
        });

        closeSearch.addEventListener('click', function () {
            searchOverlay.classList.add('d-none');
        });

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim();

            if (query.length > 2) {
                fetch(`processes/search.php?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            searchResults.innerHTML = `
                                <h2 class="results-header">${data.length} RESULT${data.length > 1 ? 'S' : ''} FOR "${query}"</h2>
                                <h3 class="results-section">ARTICLES</h3>
                                <hr>
                            `;
                            data.forEach(article => {
                                const resultItem = document.createElement('div');
                                resultItem.classList.add('search-result-item');
                                resultItem.innerHTML = `
                                    <a href="article.php?id=${article.id}">
                                        <h4 class="article-title-over">${article.name}</h4>
                                        <p class="article-author">BY ${article.author}</p>
                                    </a>
                                `;
                                searchResults.appendChild(resultItem);
                            });
                        } else {
                            searchResults.innerHTML = '<p>No results found</p>';
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                        searchResults.innerHTML = `<p>Error fetching results: ${error.message}</p>`;
                    });
            } else {
                searchResults.innerHTML = '';
            }
        });
    });
</script>
</body>
</html>
