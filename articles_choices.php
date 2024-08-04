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
              JOIN category c ON a.category_id = c.id WHERE a.date <= CURDATE()";
    if ($categoryId) {
        $query .= " AND a.category_id = :category_id";
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


<div class="container mt-4">
    <h1 class="article-list-title mt-4 mb-4">Actu des finances</h1>
    <p class="article-list-description mb-4">Bienvenue sur cette page qui regroupe tous nos articles liés à l'actualité
        financière. Vous retrouverez ici des actus pour vous tenir au courant des dernières nouveautés, sorties et
        événements qui concernent la finance.</p>

    <div class="custom-select-wrapper">
        <div class="custom-select-trigger">
            <span>Select Category</span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="custom-options">
            <span class="custom-option custom-option-all" data-value="all">Tout</span>
            <?php foreach ($categories as $category): ?>
                <span class="custom-option" data-value="<?= htmlspecialchars($category['id']); ?>">
                <?= htmlspecialchars($category['name']); ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="article-list-row" id="article-container">
        <?php foreach ($articles as $article):
            $categoryColor = htmlspecialchars($article['category_color']);
            ?>
            <div class="article-list-col-lg-3 article-list-col-md-4 article-list-col-sm-6 mb-4"
                 data-category="<?= htmlspecialchars($article['category_id']); ?>">
                <a href="article.php?id=<?= htmlspecialchars($article['id']); ?>" class="article-list-link">
                    <div class="article-list-card h-100">
                        <img class="article-list-card-img-top" src="<?= htmlspecialchars($article['image']); ?>"
                             alt="Article Image">
                        <div class="article-list-card-body">
                            <div class="d-flex justify-content-between">
                                <?php
                                $date = new DateTime($article['date']);
                                $formattedDate = strtoupper($date->format('F j, Y'));
                                ?>
                                <small class="text-muted"><?= htmlspecialchars($formattedDate); ?></small>
                                <small class="text-muted"><?= htmlspecialchars($article['author']); ?></small>
                            </div>
                            <h5 class="article-list-card-title mt-2"><?= htmlspecialchars($article['name']); ?></h5>
                        </div>
                        <div class="article-list-card-footer">
                            <span class="badge"
                                  style="background-color: <?= $categoryColor; ?>; color: #fff;"><?= htmlspecialchars($article['category_name']); ?></span>
                        </div>
                    </div>
                </a>
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

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === 1 || $i === $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <li class="page-item <?= ($page === $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php elseif ($i === $page - 3 || $i === $page + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($totalPages, $page + 1); ?>" aria-label="Next">
                    <span aria-hidden="true">Suivant &raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customSelectTrigger = document.querySelector('.custom-select-trigger');
        const customOptions = document.querySelector('.custom-options');
        const customOptionItems = document.querySelectorAll('.custom-option');

        function setOptionsWidth() {
            customOptions.style.width = `${customSelectTrigger.offsetWidth}px`;
        }

        customSelectTrigger.addEventListener('click', function (e) {
            console.log('Trigger clicked');
            e.stopPropagation();
            customOptions.classList.toggle('open');
            console.log('Dropdown state:', customOptions.classList.contains('open') ? 'open' : 'closed');
            setOptionsWidth(); // Set width on click
        });

        customOptionItems.forEach(function (option) {
            option.addEventListener('click', function (e) {
                console.log('Option clicked:', this.innerHTML);
                customSelectTrigger.innerHTML = `${this.innerHTML} <i class="fas fa-chevron-down"></i>`;
                customOptions.classList.remove('open');
                e.stopPropagation();

                var value = this.getAttribute('data-value');
                var url = 'articles_choices.php';

                if (value !== 'all') {
                    url += '?category_id=' + value;
                }

                console.log('Redirecting to:', url);
                window.location.href = url;
            });
        });

        document.addEventListener('click', function (e) {
            if (!customSelectTrigger.contains(e.target) && customOptions.classList.contains('open')) {
                console.log('Click outside, closing dropdown');
                customOptions.classList.remove('open');
            }
        });

        window.addEventListener('resize', setOptionsWidth); // Adjust width on window resize
        setOptionsWidth(); // Set initial width
    });


</script>
<!-- Include jQuery if it's not already included -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include the Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- Insert the rest of your page content here -->
<?php include 'footer.php'; ?>
