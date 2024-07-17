<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../main');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

// DSN for PDO MySQL connection
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create a PDO instance
    $pdo = new PDO($dsn, $user, $password);
    // Set PDO error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log("Connected to the database $dbname successfully.");
} catch (PDOException $e) {
    error_log("Connection error: " . $e->getMessage());
    // Handle the error gracefully
    die("Database connection failed: " . $e->getMessage());
}
?>
