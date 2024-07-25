<?php
session_start();
ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');


// Include the database configuration file
require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparer la requête SQL pour éviter les injections SQL
    $stmt = $pdo->prepare("SELECT id, username, password,credential_id FROM user WHERE credential_id!='5' AND username = ?");
    $stmt->bindParam(1, $username);

    // Exécuter la requête
    $stmt->execute();

    // Récupérer le premier résultat
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Vérifier si le mot de passe correspond
        if (password_verify($password, $row['password'])) {
            // Si c'est le cas, définir les variables de session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['credential_id'] = $row['credential_id'];

            // Rediriger vers la page d'accueil de l'admin
            header("Location: ../index_admin.php");
            exit;
        } else {
            // Si les identifiants sont incorrects, définir un message d'erreur
            $_SESSION['error'] = "Invalid username or password";
            header("Location: ../admin_login.php");
            exit;
        }
    } else {
        // Si le nom d'utilisateur n'existe pas, définir un message d'erreur
        $_SESSION['error'] = "Invalid username or password";
        header("Location: ../admin_login.php");
        exit;
    }
}
$conn = null;
?>