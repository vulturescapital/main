<?php
// Vérifiez que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Assurez-vous que l'email est valide.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirigez vers une page d'erreur ou affichez un message.
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=invalid_email');
        exit;
    }

    // Inclure le fichier de configuration de la base de données
    require_once 'dbconfig.php';

    try {
        // Vérifier si l'e-mail est déjà inscrit
        $checkStmt = $pdo->prepare("SELECT * FROM newsletter WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->rowCount() > 0) {
            // L'adresse e-mail est déjà utilisée
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=email_exists');
            exit;
        }

        // Préparez la requête SQL pour insérer un nouvel e-mail
        $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (:email)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Redirection vers la page d'origine avec un paramètre de succès
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=1');
        exit();

    } catch (PDOException $e) {
        // Log l'erreur pour une révision par un administrateur
        error_log("Erreur lors de l'inscription : " . $e->getMessage());
        // Redirigez vers la page d'origine avec un paramètre d'erreur
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=db_error');
        exit();
    }
} else {
    // Si les données du formulaire ne sont pas reçues, redirigez vers la page du formulaire
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
