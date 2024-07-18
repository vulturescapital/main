<?php
// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['csrf_token'])) {
    // Validate CSRF token
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Validate the email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?error=invalid_email');
            exit;
        }

        // Include the database configuration file
        require_once 'dbconfig.php';  // Ensure this file correctly sets up $pdo

        try {
            // Check if the email is already subscribed
            $checkStmt = $pdo->prepare("SELECT * FROM newsletter WHERE email = :email");
            $checkStmt->execute([':email' => $email]);
            if ($checkStmt->rowCount() > 0) {
                header('Location: index.php?error=email_exists');
                exit;
            }

            // Prepare the SQL query to insert a new email
            $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (:email)");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Redirect to index.php with a success parameter
            header('Location: index.php?success=1');
            exit;

        } catch (PDOException $e) {
            // Log the error for review by an admin
            error_log("Error during subscription: " . $e->getMessage());

            // Redirect to index.php with an error parameter
            header('Location: index.php?error=db_error');
            exit;
        }
    } else {
        // Invalid CSRF token
        header('Location: index.php?error=invalid_csrf');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

// End output buffering and flush the output
ob_end_flush();
?>

