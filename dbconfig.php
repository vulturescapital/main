<?php
$host = 'vulturescapital.fr'; // Remplacez par l'adresse du serveur fournie par O2switch
$dbname = 'kinu7234_vultures'; // Nom de votre base de données
$user = 'kinu7234_romain'; // Nom d'utilisateur de la base de données
$password = 'Napoleon0304!'; // Mot de passe de la base de données

// DSN pour la connexion PDO à MySQL
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Créez une instance de PDO pour établir une connexion à la BDD
    $pdo = new PDO($dsn, $user, $password);
    // Configurez le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log("Connecté à la base de données $dbname avec succès.");
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
}
session_start(); // Start the session at the top of your script
?>