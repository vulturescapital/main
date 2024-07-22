<?php
include 'header.php'; ?>

<?php
$articlesPerPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$startAt = ($page - 1) * $articlesPerPage;

try {
    $categories = $pdo->query("SELECT id, name FROM category")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

try {
    $queryParams = [];
    $query = "SELECT a.*, c.color AS category_color, c.name AS category_name
              FROM article a
              JOIN category c ON a.category_id = c.id";
    if ($categoryId) {
        $query .= " WHERE a.category_id = :category_id";
        $queryParams[':category_id'] = $categoryId;
    }
    $query .= " ORDER BY a.id DESC LIMIT :startAt, :articlesPerPage";

    $articleStmt = $pdo->prepare($query);
    $articleStmt->bindValue(':startAt', $startAt, PDO::PARAM_INT);
    $articleStmt->bindValue(':articlesPerPage', $articlesPerPage, PDO::PARAM_INT);
    if ($categoryId) {
        $articleStmt->bindParam(':category_id', $categoryId);
    }
    $articleStmt->execute();
    $articles = $articleStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total count for pagination
    $totalQuery = "SELECT COUNT(*) FROM article";
    if ($categoryId) {
        $totalQuery .= " WHERE category_id = :category_id";
    }
    $totalStmt = $pdo->prepare($totalQuery);
    if ($categoryId) {
        $totalStmt->bindParam(':category_id', $categoryId);
    }
    $totalStmt->execute();
    $totalArticles = $totalStmt->fetchColumn();
    $totalPages = ceil($totalArticles / $articlesPerPage);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<body>
<div class="container mt-4">
    <!--<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Actu des finances</li>
        </ol>-->
    </nav>
    <h1 class="mt-4 mb-4">Actu des finances</h1>
    <p class="mb-4">Bienvenue sur cette page qui regroupe tous nos articles liés à l'actualité financière. Vous
        retrouverez ici des actus pour vous tenir au courant des dernières nouveautés, sorties et événements qui
        concernent la finance.</p>

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
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4"
                 data-category="<?= htmlspecialchars($article['category_id']); ?>">
                <a href="article.php?id=<?= htmlspecialchars($article['id']); ?>" class="article-link">
                    <div class="card h-100">
                        <img class="card-img-top" src="<?= htmlspecialchars($article['image']); ?>" alt="Article Image">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <?php
                                // Format and display the publication date
                                $date = new DateTime($article['date']);
                                $formattedDate = strtoupper($date->format('F j, Y'));
                                ?>
                                <small class="text-muted"><?= htmlspecialchars($formattedDate); ?></small>
                                <small class="text-muted"><?= htmlspecialchars($article['author']); ?></small>
                            </div>
                            <h5 class="card-title mt-2"><?= htmlspecialchars($article['name']); ?></h5>
                        </div>
                        <div class="card-footer bg-white">
                            <span class="badge"
                                  style="background-color: <?= $categoryColor; ?>; color: #fff;"><?= htmlspecialchars($article['category_name']); ?></span>
                        </div>
                    </div>
            </div>
        <?php endforeach; ?>
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max(1, $page - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo; Précédent</span>
                </a>
            </li>

            <!-- Numbered page links -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === 1 || $i === $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <li class="page-item <?= ($page === $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php elseif ($i === $page - 3 || $i === $page + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Next Button -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($totalPages, $page + 1); ?>" aria-label="Next">
                    <span aria-hidden="true">Suivant &raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script>
    $('.custom-option').on('click', function () {
        var value = $(this).data('value');
        var url = 'articles_choices.php';

        // If 'All' is selected, do not add the 'category_id' parameter
        if (value !== 'all') {
            url += '?category_id=' + value;
        }

        window.location.href = url;
    });

</script>
<!-- Include jQuery if it's not already included -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include the Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- Insert the rest of your page content here -->
</div>
<?php include 'footer.php'; ?>
