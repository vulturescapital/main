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

// Check if the logged-in user has the right credentials to add a new user
try {
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($logged_in_user_credential['level_name'] != 'Admin') {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour ajouter un utilisateur.";
        header("Location: ../admin_users.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la vérification des droits: " . $e->getMessage();
    header("Location: ../admin_users.php");
    exit;
}

// Function to validate password against the criteria
function validate_password($password) {
    return strlen($password) >= 12 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/\d/', $password) &&
        preg_match('/[!@#\$%\^\&*\)\(+=._-]/', $password);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $surname = filter_var(trim($_POST['surname']), FILTER_SANITIZE_STRING);
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Password should not be sanitized as it may change its intended value
    $credential_id = filter_var($_POST['credential_id'], FILTER_SANITIZE_NUMBER_INT);

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'adresse e-mail n'est pas valide.";
        header("Location: ../admin_add_user.php");
        exit;
    }

    // Validate the password
    if (!validate_password($password)) {
        $_SESSION['error'] = "Le mot de passe ne respecte pas les règles de sécurité.";
        header("Location: ../admin_add_user.php");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO user (name, surname, username, email, password, credential_id) VALUES (:name, :surname, :username, :email, :password, :credential_id)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':credential_id', $credential_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Nouvel utilisateur ajouté avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur: " . $e->getMessage();
    }

    header("Location: ../admin_users.php");
    exit;
}

// End output buffering and flush the output
ob_end_flush();
?>
