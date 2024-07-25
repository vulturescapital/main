<?php
include 'header_admin.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

try {
    // Fetch all articles from the database
    $stmt = $pdo->query("SELECT * FROM article");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch(PDOException $e) {
    // If there's an error, return it as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<div class="main-content">
    <h1>Liste des Articles</h1>
    <div class="card-container">
        <?php foreach ($articles as $article): ?>
            <div class="card">
                <?php if (!empty($article['image'])): ?>
                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="Image de <?= htmlspecialchars($article['name']) ?>">
                <?php endif; ?>
                <div class="card-content">
                    <h2 title="<?= htmlspecialchars($article['name']) ?>"><?= htmlspecialchars($article['name']) ?></h2>
                    <div>
                        <p>Par <?= htmlspecialchars($article['author']) ?></p>
                        <p>Le <?= date('d/m/Y', strtotime($article['date'])) ?></p>
                        <p>Vues: <?= htmlspecialchars($article['views']) ?></p>
                    </div>
                </div>
                <div class="quick-actions">
                    <a href="admin_modify_article.php?id=<?= $article['id'] ?>" class="modify-btn">Modifier</a>
                    <a href="processes/admin_remove_article.php?id=<?= $article['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '<?= htmlspecialchars($_SESSION['success']) ?>'
        });
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '<?= htmlspecialchars($_SESSION['error']) ?>'
        });
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
</script>