<?php
define('SECURE_ACCESS', true);

session_start();
ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');

// Include the database configuration file
require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // Password should not be sanitized as it may change its intended value

    try {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT id, username, password, credential_id FROM user WHERE credential_id != '5' AND username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch the first result
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // If the password is correct, set the session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['credential_id'] = $row['credential_id'];

                // Regenerate session ID to prevent session fixation attacks
                session_regenerate_id(true);

                // Redirect to the admin home page
                header("Location: ../index_admin.php");
                exit;
            } else {
                // If the credentials are incorrect, set an error message
                $_SESSION['error'] = "Invalid username or password";
                header("Location: ../admin_login.php");
                exit;
            }
        } else {
            // If the username does not exist, set an error message
            $_SESSION['error'] = "Invalid username or password";
            header("Location: ../admin_login.php");
            exit;
        }
    } catch (PDOException $e) {
        // Log the error and set a session error message
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again later.";
        header("Location: ../admin_login.php");
        exit;
    }
}

// End output buffering and flush the output
ob_end_flush();
$pdo = null;
?>
