<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

header('Content-Type: application/json');

session_start(); // Start the session

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query']) && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        if ($_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid CSRF token");
        }

        if (!$pdo) {
            throw new Exception("Database connection failed");
        }

        $query = $_GET['query'];
        $sql = "SELECT id, name, author, date, image, file, duree_reading, texte, header FROM article WHERE name LIKE :searchTerm OR texte LIKE :searchTerm";
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed");
        }
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($articles);
    } else {
        throw new Exception("Invalid request");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
