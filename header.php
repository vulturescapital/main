<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'dbconfig.php'; ?>
    <?php
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <script>
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
    <title>Vultures Blog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/search-overlay.css"> <!-- Add this line to include the CSS for the overlay -->
</head>
<body class="d-flex flex-column min-vh-100 light-mode">
<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a href="index.php" class="navbar-brand">Vultures</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="./articles_choices.php" class="nav-link">Articles</a></li>
                    <li class="nav-item"><a href="./contact.php" class="nav-link">Contact</a></li>
                    <li class="nav-item"><a href="./newsletter.php" class="nav-link">Newsletter</a></li>
                    <li class="nav-item search-icon">
                        <a href="javascript:void(0);" class="nav-link" id="search-toggle"><i class="fas fa-search"></i></a>
                    </li>
                </ul>
                <button id="theme-toggle" class="btn">
                    <i class="fa fa-moon"></i>
                    <i class="fa fa-sun"></i>
                </button>
            </div>
        </div>
    </nav>
</header>

<!-- Search Overlay -->
<div id="search-overlay" class="search-overlay d-none">
    <div class="search-overlay-content">
        <button id="close-search" class="close-search">&times;</button>
        <input type="text" id="search-input" class="search-input" placeholder="Search Vultures Blog">
        <div id="search-results" class="search-results">
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="../js/main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchToggle = document.getElementById('search-toggle');
        const searchOverlay = document.getElementById('search-overlay');
        const closeSearch = document.getElementById('close-search');
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');

        // Toggle search overlay visibility
        searchToggle.addEventListener('click', function () {
            searchOverlay.classList.remove('d-none');
            searchInput.focus();  // Focus on the search input when it becomes visible
        });

        // Close search overlay
        closeSearch.addEventListener('click', function () {
            searchOverlay.classList.add('d-none');
        });

        // Live search functionality
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
                                throw new Error(text)
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
                                        <h4 class="article-title">${article.name}</h4>
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

        // Theme toggle functionality
        const themeToggleButton = document.getElementById('theme-toggle');
        themeToggleButton.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            document.body.classList.toggle('light-mode');
        });
    });
</script>
<style>
    .search-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--background-color-light);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
        box-sizing: border-box;
    }

    .search-overlay-content {
        width: 90%;
        margin: 0 auto;
    }

    .close-search {
        align-self: flex-end;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 10px 0;
        font-size: 18px;
        border: none;
        border-bottom: 1px solid #000;
        background: none;
        outline: none;
        margin-bottom: 20px;
    }

    .search-results {
        width: 90%;
        background-color: var(--background-color-light);
    }

    .results-header {
        font-size: 14px;
        font-weight: normal;
        margin-bottom: 10px;
    }

    .results-section {
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .search-result-item {
        border-bottom: 1px solid #eee;
    }

    .search-result-item a {
        text-decoration: none;
        color: black;
        display: block;
    }

    .article-title {
        font-size: 18px;
        font-weight: normal;
        margin: 0;
    }

    .article-author {
        font-size: 12px;
        color: #888;
        margin-top: 5px;
    }
    .search-result-item a:hover {
        background-color: transparent;
    }
</style>
</body>
</html>
