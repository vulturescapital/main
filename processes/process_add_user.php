<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

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
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $credential_id = $_POST['credential_id'];

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
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':credential_id', $credential_id);

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
?>
