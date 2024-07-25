<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the logged-in user has the right credentials to modify a user
try {
    $stmt = $pdo->prepare("SELECT c.level_name,u.credential_id FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_credential_id = $logged_in_user_credential['credential_id'];
    if ($logged_in_user_credential['level_name'] != 'Admin' && $_SESSION['user_id'] != $_POST['id']) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour modifier cet utilisateur.";
        header("Location: ../admin_users.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la vérification des droits: " . $e->getMessage();
    header("Location: ../admin_users.php");
    exit;
}

// Function to validate password against the criteria
function validate_password($password)
{
    return strlen($password) >= 12 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/\d/', $password) &&
        preg_match('/[!@#\$%\^\&*\)\(+=._-]/', $password);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $credential_id = $_POST['credential_id'];
    $password = $_POST['password'];


    if ($logged_in_user_credential['level_name'] != 'Admin' && $_SESSION['user_id'] == $id && $credential_id != $current_credential_id) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour modifier vos propres credentials.";
        header("Location: ../admin_modify_user.php?id=$id");
        exit;
    }
    // Validate the password if it's being changed
    if (!empty($password) && !validate_password($password)) {
        $_SESSION['error'] = "Le mot de passe ne respecte pas les règles de sécurité.";
        header("Location: ../admin_modify_user.php?id=$id");
        exit;
    }

    $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    try {
        // Update the user in the database
        if ($password_hash) {
            $stmt = $pdo->prepare("UPDATE user SET name = :name, surname = :surname, username = :username, email = :email, password = :password, credential_id = :credential_id WHERE id = :id");
            $stmt->bindParam(':password', $password_hash);
        } else {
            $stmt = $pdo->prepare("UPDATE user SET name = :name, surname = :surname, username = :username, email = :email, credential_id = :credential_id WHERE id = :id");
        }
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':credential_id', $credential_id);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Utilisateur modifié avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'utilisateur.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la modification de l'utilisateur: " . $e->getMessage();
    }

    header("Location: ../admin_users.php");
    exit;
}
?>
