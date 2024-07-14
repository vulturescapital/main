<?php
include 'dbconfig.php';
$article = [];
$related_articles = [];

// Validate the ID and fetch article details.
if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    // Prepare and execute the statement to select the article.
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id = :id");
    $stmt->execute([':id' => $article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($article) {
        if (empty($_SESSION['viewed_articles']) || !in_array($article_id, $_SESSION['viewed_articles'])) {
            // Increment the views count for the article
            $update_stmt = $pdo->prepare("UPDATE article SET views = views + 1 WHERE id = :id");
            $update_stmt->execute([':id' => $article_id]);

            // Store article ID in session to prevent repeated counts during the session
            $_SESSION['viewed_articles'][] = $article_id;
        }

        // Fetch the text content of all other articles
        $stmt = $pdo->prepare("SELECT id, texte FROM article WHERE id != :id");
        $stmt->execute([':id' => $article_id]);
        $allOtherArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Levenshtein distances
        $levenshteinDistances = [];
        foreach ($allOtherArticles as $otherArticle) {
            $levDist = levenshtein($article['texte'], $otherArticle['texte']);
            $levenshteinDistances[$otherArticle['id']] = $levDist;
        }

        // Sort articles by their Levenshtein distance (smallest distance first)
        asort($levenshteinDistances);

        // Get the IDs of the four most related articles
        $closestMatchesIds = array_keys(array_slice($levenshteinDistances, 0, 4, true));

        // Fetch the related articles with the closest match
        $inQuery = implode(',', array_fill(0, count($closestMatchesIds), '?'));
        $related_articles_stmt = $pdo->prepare("SELECT * FROM article WHERE id IN ($inQuery)");
        $related_articles_stmt->execute($closestMatchesIds);
        $related_articles = $related_articles_stmt->fetchAll(PDO::FETCH_ASSOC);

        $most_viewed_stmt = $pdo->prepare("SELECT * FROM article WHERE id != :id ORDER BY views DESC LIMIT 5");
        $most_viewed_stmt->execute([':id' => $article_id]);
        $most_viewed_articles = $most_viewed_stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        header('Location: errorpage.php'); // Redirect to an error page if no article is found.
        exit;
    }
} else {
    header('Location: errorpage.php'); // Redirect to an error page if ID is invalid.
    exit;
}

// Including the header part of your HTML page
include 'header.php';
?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="articles_choices.php">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">AI is Now Shovel Ready</li>
        </ol>
    </nav>
    <div class="article-header">
        <h1 class="article-title"><?= htmlspecialchars($article['name']); ?></h1>
        <h2 class="article-subtitle"><?= htmlspecialchars($article['header']); ?></h2>
        <p class="article-author">By <?= htmlspecialchars($article['author']); ?></p>
        <?php
            $date = new DateTime($article['date']);
            $formattedDate = strtoupper($date->format('F j, Y'));
        ?>
        <p class="article-date">Published <?= htmlspecialchars($formattedDate); ?></p>
        <p class="article-reading-time"><?= htmlspecialchars($article['duree_reading']); ?> min read</p>
    </div>
    <figure class="article-image-container">
        <img src="<?= htmlspecialchars($article['image']); ?>" alt="<?= htmlspecialchars($article['name']); ?>" class="article-image">
    </figure>
    <div class="article-content">
        <section class="article-content">
            <?= $article['texte']; ?>
        </section>
    </div>
    <div class="buy-me-a-coffee-container">
        <a href="https://www.buymeacoffee.com/vultures_capital"><img src="https://img.buymeacoffee.com/button-api/?text=Buy me a coffee&emoji=☕&slug=vultures_capital&button_colour=5F7FFF&font_colour=ffffff&font_family=Lato&outline_colour=000000&coffee_colour=FFDD00" /></a>
    </div>
    <div class="related-articles-container">
        <h3>Articles Similaires</h3>
        <div class="related-container mt-4">
            <div class="related-row">
                <?php foreach ($related_articles as $related_article): ?>
                    <div class="related-col mb-4">
                        <a href="article.php?id=<?= htmlspecialchars($related_article['id']); ?>" class="related-article-link">
                            <div class="related-card">
                                <img class="related-img-top" src="<?= htmlspecialchars($related_article['image']); ?>" alt="Article Image">
                                <div class="related-card-body">
                                    <h5 class="related-title"><?= htmlspecialchars($related_article['name']); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; // Include the footer file if you have one ?>