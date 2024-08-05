<?php
// dbconfig.php

// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS') || SECURE_ACCESS !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit('Direct access forbidden.');
}

// Load Composer's autoloader
$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    die('Composer autoload file not found. Please run "composer install" in the main directory.');
}
require_once $autoloadFile;

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Validate environment variables
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'IP_ANONYMIZE_SECRET'])->notEmpty();

// Database configuration
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

// DSN for PDO connection to MySQL
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create a PDO instance to establish a connection to the database
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_TIMEOUT => 5, // Set connection timeout to 5 seconds
    ]);

    // Log successful connection
    error_log("Successfully connected to database $dbname.");
} catch (PDOException $e) {
    // Log the detailed error
    error_log("Database connection error: " . $e->getMessage());

    // For production, use a generic error message
    http_response_code(500);
    die("A database error occurred. Please try again later.");
}

// Use a more secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.sid_length', 48);
ini_set('session.sid_bits_per_character', 6);

// Start the session with improved security settings
session_start([
    'cookie_lifetime' => 3600, // Session timeout set to 1 hour
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cookie_samesite' => 'Strict'
]);

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

function check_rate_limit($pdo, $ip_address, $limit = 100, $time_frame = 3600)
{
    try {
        // Create the rate_limit table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limit (
            ip_address VARCHAR(64) PRIMARY KEY,
            request_count INT NOT NULL,
            last_request_time INT NOT NULL
        )");

        // Hash the IP address
        $ip_hash = hash('sha256', $ip_address);

        // Get the current count and last request time for this IP
        $stmt = $pdo->prepare("SELECT request_count, last_request_time FROM rate_limit WHERE ip_address = ?");
        $stmt->execute([$ip_hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $current_time = time();

        if ($row) {
            // If the time frame has passed, reset the count
            if ($current_time - $row['last_request_time'] > $time_frame) {
                $new_count = 1;
            } else {
                $new_count = $row['request_count'] + 1;
            }

            // Update the count and time
            $stmt = $pdo->prepare("UPDATE rate_limit SET request_count = ?, last_request_time = ? WHERE ip_address = ?");
            $stmt->execute([$new_count, $current_time, $ip_hash]);
        } else {
            // First request from this IP
            $stmt = $pdo->prepare("INSERT INTO rate_limit (ip_address, request_count, last_request_time) VALUES (?, 1, ?)");
            $stmt->execute([$ip_hash, $current_time]);
            $new_count = 1;
        }

        // Check if the limit has been exceeded
        return $new_count > $limit;
    } catch (PDOException $e) {
        // Log the error and return false to allow the request in case of a database error
        error_log("Rate limiting error: " . $e->getMessage());
        return false;
    }
}

// Use the rate limiting function
$user_ip = $_SERVER['REMOTE_ADDR'];
if (check_rate_limit($pdo, $user_ip, 100, 3600)) {
    header("HTTP/1.1 429 Too Many Requests");
    exit('Rate limit exceeded. Please try again later.');
}

?>
