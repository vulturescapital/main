<?php
include 'dbconfig.php';

// Check for an ID in the query string and validate it
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    // Prepare a statement to select the article
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $article_id]);

    // Fetch the article
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    // If article is not found, redirect or handle the error appropriately
    if (!$article) {
        // Redirect to a different page or show an error
        header('Location: errorpage.php');
        exit;
    }
} else {
    // Redirect to a different page or show an error if ID is not set or not valid
    header('Location: errorpage.php');
    exit;
}

// Including the header part of your HTML page
include 'header.php';
?>

<div class="container mt-5 article-page">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['nom']); ?></li>
        </ol>
    </nav>

    <article>
        <header class="article-header">
            <h1 class="article-title"><?= htmlspecialchars($article['nom']); ?></h1>
            <h2 class="article-subtitle"><?= htmlspecialchars($article['header']); ?></h2>
            <p class="article-meta"><?= date("j F Y", strtotime($article['date'])); ?> at <?= date("g:i a", strtotime($article['date'])); ?> | <?= htmlspecialchars($article['duree_reading']); ?> minutes read</p>
        </header>

        <figure class="article-image-container">
            <img src="<?= htmlspecialchars($article['images']); ?>" alt="<?= htmlspecialchars($article['nom']); ?>" class="article-image">
        </figure>

        <section class="article-content">
            <?= nl2br(htmlspecialchars($article['texte'])); ?>
        </section>
    </article>
</div>

<?php include 'footer.php'; // Include the footer file if you have one ?>