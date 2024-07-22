<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');

require_once 'dbconfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM article WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['delete_status'] = "Article supprimé avec succès.";
        } else {
            $_SESSION['delete_status'] = "Aucun article trouvé avec cet ID.";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_status'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    $_SESSION['delete_status'] = "ID d'article non spécifié.";
}

// Redirection vers la page de liste des articles
header("Location: ../admin_post.php");
exit;
?>