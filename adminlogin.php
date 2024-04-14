<?php include 'header.php'; ?>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="adminlogin.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
<?php
// Inclure le fichier de connexion à la base de données ici
include 'dbconfig.php';

$mot_de_passe = 'aze'; // Le mot de passe en texte clair
$mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
echo $mot_de_passe_hache; // Utilisez cette chaîne hachée pour mettre à jour la base de données


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Préparer la requête SQL pour éviter les injections SQL
    $stmt = $pdo->prepare("SELECT id, username, password FROM utilisateurs WHERE username = ?");
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

            // Rediriger vers la page d'accueil de l'admin
            header("Location: index_admin.php");
            exit;
        } else {
            // Si les identifiants sont incorrects, rediriger vers la page de connexion avec un message d'erreur
            header("Location: adminlogin.php?error=invalid");
            exit;
        }
    } else {
        // Si le nom d'utilisateur n'existe pas, rediriger avec un message d'erreur
        header("Location: adminlogin.php?error=invalid");
        exit;
    }
}

// Inclure le pied de page
include 'footer.php';
?>
