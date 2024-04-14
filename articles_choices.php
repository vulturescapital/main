<?php include 'header.php'; ?>
<?php
include 'dbconfig.php';

// Fetch categories from the database
try {
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
try {
    $stmt = $pdo->query("SELECT a.*, c.color as category_color,c.id as category_id
                        FROM articles a
                        JOIN categories c ON a.tags = c.name");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// HTML and PHP code to display the categories in a dropdown
?>
<body>
    <section class="newsletter-section">
        <div class="container">
            <h2>Newsletter</h2>
            <p>Recevez un récapitulatif de l'actualité chaque lundi Et c'est tout.</p>
            <form action="add_email_process.php" method="POST">
                <input type="email" name="email" placeholder="Votre adresse e-mail" required>
                <button type="submit" class="btn-newsletter">S'inscrire !</button>
                <div class="checkbox">
                    <input type="checkbox" id="consent" required>
                    <label for="consent">Oui, j'accepte de recevoir votre newsletter selon votre politique de confidentialité</label>
                </div>
            </form>
        </div>
    </section>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Actu des finances</li>
            </ol>
        </nav>
        <h1 class="mt-4 mb-4">Actu des finances</h1>
        <p class="mb-4">Bienvenue sur cette page qui regroupe tous nos articles liés à l'actualité financière. Vous retrouverez ici des actus pour vous tenir au courant des dernières nouveautés, sorties et événements qui concernent la finance.</p>

        <div class="custom-select-wrapper">
            <div class="custom-select-trigger">
                <span>Select Category</span>
                <i class="fas fa-chevron-down"></i>
            </div>
                <div class="custom-options">
                    <span class="custom-option custom-option-all" data-value="all">Tout</span> <!-- Appliquez la classe ici -->
                    <?php foreach ($categories as $category): ?>
                        <span class="custom-option" data-value="<?= htmlspecialchars($category['id']); ?>">
                            <?= htmlspecialchars($category['name']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="container mt-4" id="article-container">
            <div class="row">
                <?php foreach ($articles as $article):
                    // Assuming you have a JOIN to get the color from the categories table
                    $categoryColor = htmlspecialchars($article['category_color']);
                ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-category="<?= htmlspecialchars($article['category_id']); ?>">
                        <a href="article.php?id=<?= htmlspecialchars($article['id']); ?>" class="article-link">
                        <div class="card h-100">
                            <img class="card-img-top" src="<?= htmlspecialchars($article['images']); ?>" alt="Article Image">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted"><?= htmlspecialchars($article['date']); ?></small>
                                    <small class="text-muted"><?= htmlspecialchars($article['author']); ?></small>
                                </div>
                                <h5 class="card-title mt-2"><?= htmlspecialchars($article['nom']); ?></h5>
                            </div>
                            <div class="card-footer bg-white">
                                <span class="badge" style="background-color: <?= $categoryColor; ?>; color: #fff;"><?= htmlspecialchars($article['tags']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>





        <!-- Include jQuery if it's not already included -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Include the Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

        <!-- Initialize Select2 and your custom scripts -->
        <script src="path_to_your_custom_scripts.js"></script>
        <!-- Insert the rest of your page content here -->
    </div>
</body>
