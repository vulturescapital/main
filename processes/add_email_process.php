<?php
define('SECURE_ACCESS', true);

session_start();
ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');

function get_clean_referer_url()
{
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER['HTTP_REFERER'];
        $parsed_url = parse_url($url);
        parse_str($parsed_url['query'] ?? '', $query_params);

        unset($query_params['success'], $query_params['error']);

        $clean_url = '';
        if (!empty($parsed_url['path'])) {
            $clean_url .= $parsed_url['path'];
        }
        if (!empty($query_params)) {
            $clean_url .= '?' . http_build_query($query_params);
        }
        return $clean_url;
    }
    return '/';
}

function append_query_params($url, $params)
{
    $parsed_url = parse_url($url);
    parse_str($parsed_url['query'] ?? '', $query_params);
    $query_params = array_merge($query_params, $params);

    $clean_url = '';
    if (!empty($parsed_url['path'])) {
        $clean_url .= $parsed_url['path'];
    }
    if (!empty($query_params)) {
        $clean_url .= '?' . http_build_query($query_params);
    }
    return $clean_url;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        require_once 'dbconfig.php';

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . append_query_params(get_clean_referer_url(), ['error' => 'invalid_email']));
            exit;
        }

        try {
            $pdo->beginTransaction();

            $checkStmt = $pdo->prepare("SELECT 1 FROM user WHERE email = :email AND credential_id='5'");
            $checkStmt->execute(['email' => $email]);
            if ($checkStmt->rowCount() > 0) {
                $pdo->rollBack();
                header('Location: ' . append_query_params(get_clean_referer_url(), ['error' => 'email_exists']));
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO user (email) VALUES (:email)");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $pdo->commit();

            header('Location: ' . append_query_params(get_clean_referer_url(), ['success' => '1']));
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error during subscription: " . $e->getMessage());
            header('Location: ' . append_query_params(get_clean_referer_url(), ['error' => 'db_error']));
            exit;
        } finally {
            $pdo = null;
        }
    } else {
        header('Location: ' . append_query_params(get_clean_referer_url(), ['error' => 'invalid_csrf']));
        exit;
    }
} else {
    header('Location: ' . get_clean_referer_url());
    exit;
}

ob_end_flush();
?>
