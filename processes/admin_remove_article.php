<?php
define('SECURE_ACCESS', true);

session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the article ID is provided and valid
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['delete_status'] = "ID d'article non spécifié ou invalide.";
    header("Location: ../admin_post.php");
    exit;
}

$article_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    $pdo->beginTransaction();

    // Fetch the credential level of the logged-in user
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the logged-in user has the right credentials to delete an article
    if (!in_array($logged_in_user_credential['level_name'], ['Admin', 'Editor'])) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour supprimer un article.";
        header("Location: ../admin_post.php");
        exit;
    }

    // Fetch the image path associated with the article
    $stmt = $pdo->prepare("SELECT image FROM article WHERE id = :id");
    $stmt->bindParam(':id', $article_id, PDO::PARAM_INT);
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
        $stmt->bindParam(':id', $article_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['delete_status'] = "Article supprimé avec succès.";

            // Delete the associated image file
            if (file_exists($absoluteImagePath)) {
                if (unlink($absoluteImagePath)) {
                    error_log("Image deleted successfully: " . $absoluteImagePath);
                } else {
                    error_log("Failed to delete the image: " . $absoluteImagePath);
                }
            } else {
                error_log("Image file does not exist: " . $absoluteImagePath);
            }

            $pdo->commit();
        } else {
            $_SESSION['delete_status'] = "Aucun article trouvé avec cet ID.";
            $pdo->rollBack();
        }
    } else {
        $_SESSION['delete_status'] = "Aucun article trouvé avec cet ID.";
        $pdo->rollBack();
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['delete_status'] = "Erreur lors de la suppression : " . $e->getMessage();
    error_log("Error during deletion: " . $e->getMessage());
}

// Redirect to the article list page
header("Location: ../admin_post.php");
exit;
?>
