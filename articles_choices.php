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
            <div class="custom-select-trigger">Select Category</div>
                <div class="custom-options">
                    <span class="custom-option" data-value="all">All</span> <!-- The 'All' option -->
                    <?php foreach ($categories as $category): ?>
                        <span class="custom-option" data-value="<?= htmlspecialchars($category['id']); ?>">
                            <?= htmlspecialchars($category['name']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div id="selected-category">Selected category: None</div>

        <!-- Include jQuery if it's not already included -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Include the Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

        <!-- Initialize Select2 and your custom scripts -->
        <script src="path_to_your_custom_scripts.js"></script>
        <!-- Insert the rest of your page content here -->

    </div>
</body>
