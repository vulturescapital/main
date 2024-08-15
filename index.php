<?php
define('SECURE_ACCESS', true);
// Include header
require_once 'header.php';

try {
    // Récupérer les articles les plus lus
    $mostReadQuery = "SELECT * FROM article ORDER BY views DESC LIMIT 5";
    $mostReadStmt = $pdo->query($mostReadQuery);
    $mostReadArticles = $mostReadStmt->fetchAll(PDO::FETCH_ASSOC);

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
<div class="index-carousel-wrapper">
    <div class="index-gallery js-flickity"
         data-flickity-options='{ "wrapAround": true, "prevNextButtons": false, "cellAlign": "center" }'>
        <?php foreach ($mostReadArticles as $article): ?>
            <div class="index-gallery-cell">
                <a href="article.php?id=<?php echo htmlspecialchars($article['id']); ?>" class="index-article-link">
                    <?php if ($article['image']): ?>
                        <img src="<?php echo htmlspecialchars($article['image']); ?>"
                             alt="<?php echo htmlspecialchars($article['name']); ?>" class="index-carousel-image">
                    <?php endif; ?>
                    <div class="index-gallery-author"><?php echo htmlspecialchars($article['author']); ?></div>
                    <div class="index-gallery-title"><?php echo htmlspecialchars($article['name']); ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="container">
    <?php foreach ($categories as $category): ?>
        <?php if (isset($articlesByCategory[$category['id']]) && count($articlesByCategory[$category['id']]) > 0): ?>
            <section class="index-category-section">
                <div class="index-category-header">
                    <h2 class="index-category-title"><?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="index-arrows">
                        <button type="button" class="index-arrow-btn index-left" onclick="scrollArticlesLeft(this)"
                                aria-label="Previous slide">
                            <svg viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M0.292892 0.292894C0.683416 -0.0976306 1.31658 -0.0976315 1.70711 0.292892L7.00002 5.58579L12.2929 0.292894C12.6834 -0.0976306 13.3166 -0.0976315 13.7071 0.292892C14.0976 0.683416 14.0976 1.31658 13.7071 1.70711L7.70713 7.70711C7.51959 7.89464 7.26524 8 7.00002 8C6.7348 8 6.48045 7.89464 6.29291 7.70711L0.292894 1.70711C-0.0976306 1.31658 -0.0976315 0.683419 0.292892 0.292894Z"
                                      fill="currentColor"></path>
                            </svg>
                        </button>
                        <button type="button" class="index-arrow-btn index-right" onclick="scrollArticlesRight(this)"
                                aria-label="Next slide">
                            <svg viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M0.292892 0.292894C0.683416 -0.0976306 1.31658 -0.0976315 1.70711 0.292892L7.00002 5.58579L12.2929 0.292894C12.6834 -0.0976306 13.3166 -0.0976315 13.7071 0.292892C14.0976 0.683416 14.0976 1.31658 13.7071 1.70711L7.70713 7.70711C7.51959 7.89464 7.26524 8 7.00002 8C6.7348 8 6.48045 7.89464 6.29291 7.70711L0.292894 1.70711C-0.0976306 1.31658 -0.0976315 0.683419 0.292892 0.292894Z"
                                      fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="index-category-content">
                    <div class="index-articles">
                        <?php foreach ($articlesByCategory[$category['id']] as $article): ?>
                            <div class="index-article-card">
                                <a href="article.php?id=<?php echo htmlspecialchars($article['id']); ?>"
                                   class="index-article-link">
                                    <?php if ($article['image']): ?>
                                        <img src="<?php echo htmlspecialchars($article['image']); ?>"
                                             alt="<?php echo htmlspecialchars($article['name']); ?>"
                                             class="index-article-image">
                                    <?php endif; ?>
                                    <div class="index-article-author"><?php echo htmlspecialchars($article['author']); ?></div>
                                    <div class="index-article-title"><?php echo htmlspecialchars($article['name']); ?></div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php include "footer.php" ?>
<script>
    function scrollArticlesLeft(element) {
        const articlesDiv = element.closest('.index-category-section').querySelector('.index-articles');
        articlesDiv.scrollBy({left: -300, behavior: 'smooth'});
    }

    function scrollArticlesRight(element) {
        const articlesDiv = element.closest('.index-category-section').querySelector('.index-articles');
        articlesDiv.scrollBy({left: 300, behavior: 'smooth'});
    }
</script>