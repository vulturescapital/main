<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the logged-in user has the right credentials to delete a user
        if (!in_array($logged_in_user_credential['level_name'], ['Admin', 'Editor'])) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour mettre à jour un article.";
            header("Location: ../admin_post.php");
            exit;
        }
        // Fetch the image path associated with the article
        $stmt = $pdo->prepare("SELECT image FROM article WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            $imagePath = $article['image'];
            error_log("Fetched image path from database: " . $imagePath);

            // Construct and normalize the image path
            $baseDir = realpath(__DIR__ . '/../images');
            if ($baseDir === false) {
                $baseDir = __DIR__ . '/../images';
            }
            $absoluteImagePath = $baseDir . '/' . basename($imagePath);

            // Log the normalized image path
            error_log("Normalized Image Path: " . $absoluteImagePath);

            // Delete the article from the database
            $stmt = $pdo->prepare("DELETE FROM article WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['delete_status'] = "Article supprimé avec succès.";

                // Delete the associated image file
                if (file_exists($absoluteImagePath)) {
                    if (unlink($absoluteImagePath)) {
                        // Log successful deletion
                        error_log("Image deleted successfully: " . $absoluteImagePath);
                    } else {
                        // Log if file deletion failed
                        error_log("Failed to delete the image: " . $absoluteImagePath);
                    }
                } else {
                    // Log if the file does not exist
                    error_log("Image file does not exist: " . $absoluteImagePath);
                }
            } else {
                $_SESSION['delete_status'] = "Aucun article trouvé avec cet ID.";
            }
        } else {
            $_SESSION['delete_status'] = "Aucun article trouvé avec cet ID.";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_status'] = "Erreur lors de la suppression : " . $e->getMessage();
        error_log("Error during deletion: " . $e->getMessage());
    }
} else {
    $_SESSION['delete_status'] = "ID d'article non spécifié.";
}

// Redirection vers la page de liste des articles
header("Location: ../admin_post.php");
exit;
?>
