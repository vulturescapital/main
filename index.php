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
    $articlesQuery = "SELECT * FROM article WHERE date <= CURDATE()";
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
                <img src="<?php echo htmlspecialchars($article['image']); ?>"
                     alt="<?php echo htmlspecialchars($article['name']); ?>" class="index-carousel-image">
                <div class="index-gallery-content">
                    <h2 class="index-gallery-title"
                        id="carousel-title-<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['name']); ?></h2>
                    <a href="article.php?id=<?php echo htmlspecialchars($article['id']); ?>"
                       class="index-gallery-button">En savoir plus</a>
                </div>
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
                                    <img src="<?php echo htmlspecialchars($article['image']); ?>"
                                         alt="<?php echo htmlspecialchars($article['name']); ?>"
                                         class="index-article-image">
                                    <div class="index-article-author"><?php echo htmlspecialchars($article['author']); ?></div>
                                    <div class="index-article-title" id="article-title-<?php echo $article['id']; ?>">
                                        <?php echo htmlspecialchars($article['name']); ?>
                                    </div>
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
    // Function to preload images
    function preloadImages(images) {
        return Promise.all(images.map(src => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = resolve;
                img.onerror = reject;
                img.src = src;
            });
        }));
    }

    // Function to get average RGB
    function getAverageRGB(imgEl) {
        var blockSize = 5, // only visit every 5 pixels
            defaultRGB = {r:0,g:0,b:0}, // for non-supporting envs
            canvas = document.createElement('canvas'),
            context = canvas.getContext && canvas.getContext('2d'),
            data, width, height,
            i = -4,
            length,
            rgb = {r:0,g:0,b:0},
            count = 0;

        if (!context) {
            return defaultRGB;
        }

        height = canvas.height = imgEl.naturalHeight || imgEl.offsetHeight || imgEl.height;
        width = canvas.width = imgEl.naturalWidth || imgEl.offsetWidth || imgEl.width;

        context.drawImage(imgEl, 0, 0);

        try {
            data = context.getImageData(0, 0, width, height);
        } catch(e) {
            /* security error, img on diff domain */
            return defaultRGB;
        }

        length = data.data.length;

        while ( (i += blockSize * 4) < length ) {
            ++count;
            rgb.r += data.data[i];
            rgb.g += data.data[i+1];
            rgb.b += data.data[i+2];
        }

        // ~~ used to floor values
        rgb.r = ~~(rgb.r/count);
        rgb.g = ~~(rgb.g/count);
        rgb.b = ~~(rgb.b/count);

        return rgb;
    }

    // Function to set title color
    function setTitleColor() {
        var carouselCells = document.querySelectorAll('.index-gallery-cell');
        var articleCards = document.querySelectorAll('.index-article-card');

        function setColorForElement(img, title, author = null) {
            if (img && title) {
                var rgb = getAverageRGB(img);
                var brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
                var color = brightness > 128 ? 'black' : 'white';
                title.style.color = color;
                if (author) {
                    author.style.color = color;
                }
            }
        }

        carouselCells.forEach(function(cell) {
            var img = cell.querySelector('.index-carousel-image');
            var title = cell.querySelector('.index-gallery-title');
            setColorForElement(img, title);
        });

        articleCards.forEach(function(card) {
            var img = card.querySelector('.index-article-image');
            var title = card.querySelector('.index-article-title');
            var author = card.querySelector('.index-article-author');
            setColorForElement(img, title, author);
        });
    }

    // Function to scroll articles
    function scrollArticlesLeft(element) {
        const articlesDiv = element.closest('.index-category-section').querySelector('.index-articles');
        articlesDiv.scrollBy({left: -300, behavior: 'smooth'});
    }

    function scrollArticlesRight(element) {
        const articlesDiv = element.closest('.index-category-section').querySelector('.index-articles');
        articlesDiv.scrollBy({left: 300, behavior: 'smooth'});
    }

    // Main initialization function
    function initializeGallery() {
        // Initialize Flickity
        var elem = document.querySelector('.index-gallery');
        var flkty = new Flickity(elem, {
            wrapAround: true,
            prevNextButtons: false,
            cellAlign: 'center'
        });

        // Set initial title colors
        setTitleColor();

        // Add Flickity event listener
        flkty.on('select', function() {
            setTitleColor();
        });
    }

    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Get all carousel image sources
        const carouselImages = Array.from(document.querySelectorAll('.index-carousel-image')).map(img => img.src);

        // Preload carousel images
        preloadImages(carouselImages)
            .then(() => {
                // Initialize the gallery once images are loaded
                initializeGallery();
            })
            .catch(error => {
                console.error('Error preloading images:', error);
                // Initialize the gallery even if image preloading fails
                initializeGallery();
            });

        // Set colors for article cards (these don't need to wait for carousel images)
        setTitleColor();
    });
</script>