<?php
include 'dbconfig.php';
$article = [];
$related_articles = [];

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
        // If the article is found, fetch related articles based on tags.
        $related_articles_stmt = $pdo->prepare("SELECT * FROM article WHERE FIND_IN_SET(:category_id, category_id) AND id != :article_id LIMIT 4");
        $related_articles_stmt->execute([':category_id' => $article['category_id'], ':article_id' => $article['id']]);
        $related_articles = $related_articles_stmt->fetchAll(PDO::FETCH_ASSOC);
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

<div class="container mt-10 article-page custom-container-padding">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <!-- Nom de la catégorie ou section -->
            <li class="breadcrumb-item"><a href="articles_choices.php">Finance</a></li>
            <!-- Nom de l'article -->
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['name']); ?></li>
        </ol>
    </nav>

    <article>
        <header class="article-header">
            <h1 class="article-title"><?= htmlspecialchars($article['name']); ?></h1>
            <h2 class="article-subtitle"><?= htmlspecialchars($article['header']); ?></h2>
            <p class="article-meta"><?= date("j F Y", strtotime($article['date'])); ?> at <?= date("g:i a", strtotime($article['date'])); ?> | <?= htmlspecialchars($article['duree_reading']); ?> minutes read</p>
        </header>

        <div class="article-content-wrapper"> <!-- This is a new wrapper for image and content -->
            <figure class="article-image-container">
                <img src="<?= htmlspecialchars($article['image']); ?>" alt="<?= htmlspecialchars($article['name']); ?>" class="article-image">
            </figure>

            <section class="article-content">
                <?= nl2br(htmlspecialchars($article['texte'])); ?>
            </section>
        </div>
    </article>
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
                                    <div class="related-date-author">
                                        <small class="text-muted"><?= date("j F Y à H:i", strtotime($related_article['date'])); ?></small>
                                        <small class="text-muted"><?= htmlspecialchars($related_article['author']); ?></small>
                                    </div>
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