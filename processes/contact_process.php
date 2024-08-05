<?php
define('SECURE_ACCESS', true);

session_start();
ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');

// Include the database configuration file
require_once 'dbconfig.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['csrf_token'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Sanitize and validate inputs
        $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
        $content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);

        // Validate the email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../contact.php?error=invalid_email");
            exit;
        }

        try {
            // Prepare the SQL query to insert a new contact form submission
            $stmt = $pdo->prepare("INSERT INTO contact_form_submissions (name, email, subject, content) VALUES (:name, :email, :subject, :content)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->execute();

            // Redirect to the original page with a success parameter
            header("Location: ../contact.php?success=true");
            exit;

        } catch (PDOException $e) {
            // Log the error for review by an admin
            error_log("Error during contact form submission: " . $e->getMessage());

            // Redirect to the original page with an error parameter
            header("Location: ../contact.php?error=db_error");
            exit;
        }
    } else {
        // Invalid CSRF token
        header("Location: ../contact.php?error=invalid_token");
        exit;
    }
} else {
    header("Location: ../contact.php?error=missing_fields");
    exit;
}

// End output buffering and flush the output
ob_end_flush();
$pdo = null;
?>
