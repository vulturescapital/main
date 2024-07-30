<?php
include 'header.php';
include 'dbconfig.php'; // Assuming your PDO connection is in this file

try {
    // Récupérer l'article le plus lu
    $mostReadQuery = "SELECT * FROM article ORDER BY views DESC LIMIT 1";
    $mostReadStmt = $pdo->query($mostReadQuery);
    $mostReadArticle = $mostReadStmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer toutes les catégories
    $categoriesQuery = "SELECT * FROM category";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer tous les articles
    $articlesQuery = "SELECT * FROM article";
    $articlesStmt = $pdo->query($articlesQuery);
    $articles = $articlesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Grouper les articles par catégorie
    $articlesByCategory = [];
    foreach ($articles as $article) {
        if (!isset($articlesByCategory[$article['category_id']])) {
            $articlesByCategory[$article['category_id']] = [];
        }
        $articlesByCategory[$article['category_id']][] = $article;
    }

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des données : " . $e->getMessage());
    // You might want to handle the error more gracefully here, e.g., show a user-friendly message
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Index</title>
    <style>
        .category {
            margin-bottom: 108px;
            position: relative;
            padding-bottom: 10px; /* Add padding bottom to create space */
        }

        .category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .category h2 {
            padding-bottom: 10px;
            font-family: Söhne, sans-serif;
            margin: 0;
            flex-grow: 1;
        }

        .category-content {
            margin-top: 20px; /* Space between header and articles */
        }

        .articles {
            display: flex;
            gap: 20px;
            overflow-x: auto; /* Enable horizontal scrolling */
            white-space: nowrap; /* Prevent wrapping */
            scrollbar-width: none; /* Hide scrollbar in Firefox */
        }

        .articles::-webkit-scrollbar {
            display: none; /* Hide scrollbar in WebKit browsers */
        }

        .article {
            position: relative;
            flex: 0 0 auto; /* Prevent flex items from shrinking or growing */
            width: 300px; /* Set fixed width for articles */
            height: 400px; /* Set fixed height for articles */
        }

        .article img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4.5px;
        }

        .article .title {
            font-size: xx-large;
            position: absolute;
            bottom: 10px;
            left: 10px;
            color: white;
            padding: 5px 10px; /* Adjust padding to fit text */
            background-color: rgba(0, 0, 0, 0.1); /* Semi-transparent background */
        }

        .article .author {
            position: absolute;
            top: 10px;
            left: 10px;
            color: white;
        }

        .arrows {
            display: flex;
            align-items: center;
            gap: 10px; /* Adjust gap between arrows if needed */
        }

        .arrow-btn {
            cursor: pointer;
            color: black;
            font-size: 24px;
            padding: 10px;
            user-select: none; /* Make text unselectable */
            transition: transform 0.3s ease; /* Add transition */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 2.25rem;
            width: 2.25rem;
            background: none; /* Override any background styles from .button */
            border: none; /* Override any border styles from .button */
        }

        .arrow-btn.left:hover {
            transform: translateX(-5px); /* Slight left transition on hover */
        }

        .arrow-btn.right:hover {
            transform: translateX(5px); /* Slight right transition on hover */
        }

        .arrow-btn.left svg {
            transform: rotate(90deg); /* Rotate left arrow correctly */
        }

        .arrow-btn.right svg {
            transform: rotate(-90deg); /* Rotate right arrow correctly */
        }

        .arrow-btn svg {
            width: 0.875rem;
            height: auto;
        }

        /* Remove border when active or focused */
        .arrow-btn:focus,
        .arrow-btn:active {
            outline: none;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if ($mostReadArticle): ?>
        <section class="hero">
            <h1><?php echo htmlspecialchars($mostReadArticle['name']); ?></h1>
            <a href="article.php?id=<?php echo $mostReadArticle['id']; ?>" class="btn">Lire plus</a>
        </section>
    <?php endif; ?>

    <?php foreach ($categories as $category): ?>
        <?php if (isset($articlesByCategory[$category['id']]) && count($articlesByCategory[$category['id']]) > 0): ?>
            <section class="category">
                <div class="category-header">
                    <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="arrows">
                        <button type="button" class="arrow-btn left" onclick="scrollArticlesLeft(this)"
                                aria-label="Previous slide">
                            <svg viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M0.292892 0.292894C0.683416 -0.0976306 1.31658 -0.0976315 1.70711 0.292892L7.00002 5.58579L12.2929 0.292894C12.6834 -0.0976306 13.3166 -0.0976315 13.7071 0.292892C14.0976 0.683416 14.0976 1.31658 13.7071 1.70711L7.70713 7.70711C7.51959 7.89464 7.26524 8 7.00002 8C6.7348 8 6.48045 7.89464 6.29291 7.70711L0.292894 1.70711C-0.0976306 1.31658 -0.0976315 0.683419 0.292892 0.292894Z"
                                      fill="currentColor"></path>
                            </svg>
                        </button>
                        <button type="button" class="arrow-btn right" onclick="scrollArticlesRight(this)"
                                aria-label="Next slide">
                            <svg viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M0.292892 0.292894C0.683416 -0.0976306 1.31658 -0.0976315 1.70711 0.292892L7.00002 5.58579L12.2929 0.292894C12.6834 -0.0976306 13.3166 -0.0976315 13.7071 0.292892C14.0976 0.683416 14.0976 1.31658 13.7071 1.70711L7.70713 7.70711C7.51959 7.89464 7.26524 8 7.00002 8C6.7348 8 6.48045 7.89464 6.29291 7.70711L0.292894 1.70711C-0.0976306 1.31658 -0.0976315 0.683419 0.292892 0.292894Z"
                                      fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="category-content">
                    <div class="articles">
                        <?php foreach ($articlesByCategory[$category['id']] as $article): ?>
                            <div class="article">
                                <?php if ($article['image']): ?>
                                    <img src="<?php echo htmlspecialchars($article['image']); ?>"
                                         alt="<?php echo htmlspecialchars($article['name']); ?>">
                                <?php endif; ?>
                                <div class="author"><?php echo htmlspecialchars($article['author']); ?></div>
                                <div class="title"><?php echo htmlspecialchars($article['name']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php include "footer.php"
?>
<script>
    function scrollArticlesLeft(element) {
        const articlesDiv = element.closest('.category').querySelector('.articles');
        console.log("Scrolling left", articlesDiv); // Debugging line
        articlesDiv.scrollBy({left: -300, behavior: 'smooth'});
    }

    function scrollArticlesRight(element) {
        const articlesDiv = element.closest('.category').querySelector('.articles');
        console.log("Scrolling right", articlesDiv); // Debugging line
        articlesDiv.scrollBy({left: 300, behavior: 'smooth'});
    }
</script>
</body>
</html>