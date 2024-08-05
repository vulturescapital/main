<?php
define('SECURE_ACCESS', true);
session_start();
ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the user ID is provided and valid
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: ../admin_users.php");
    exit;
}

$user_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    $pdo->beginTransaction();

    // Fetch the credential level of the logged-in user
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the logged-in user has the right credentials to delete a user
    if (!in_array($logged_in_user_credential['level_name'], ['Admin'])) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour supprimer un utilisateur.";
        header("Location: ../admin_users.php");
        exit;
    }

    // Check the total number of users in the database
    $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM user");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['user_count'] <= 1) {
        $_SESSION['error'] = "Impossible de supprimer le seul utilisateur restant.";
        header("Location: ../admin_users.php");
        exit;
    }

    // Check if the user to be deleted is the currently logged-in user
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Vous ne pouvez pas vous supprimer vous-même.";
        header("Location: ../admin_users.php");
        exit;
    }

    // Proceed to delete the user
    $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pdo->commit();
        $_SESSION['success'] = "Utilisateur supprimé avec succès.";

        // If the deleted user was the currently logged-in user, log them out and redirect to admin login
        if ($user_id == $_SESSION['user_id']) {
            session_destroy();
            header("Location: ../admin_login.php");
            exit;
        }
    } else {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
    }

    header("Location: ../admin_users.php");
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error during user deletion: " . $e->getMessage());
    $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
    header("Location: ../admin_users.php");
    exit;
} finally {
    $pdo = null;
}

ob_end_flush();
?>
