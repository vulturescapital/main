<?php
define('SECURE_ACCESS', true);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    if (!empty($_GET['query'])) {
        $query = $_GET['query'];
        $sql = "SELECT * FROM article WHERE name LIKE ? OR texte LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
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
