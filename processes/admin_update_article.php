<?php
define('SECURE_ACCESS', true);

session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

function calculateExtraReadingTimeForImages($content)
{
    $initialSecondsPerImage = 5; // seconds per image
    $minimumSecondsPerImage = 1;
    $extraReadingTimeInSeconds = 0;
    $imageCount = substr_count($content, '<img');

    for ($i = 0; $i < $imageCount; $i++) {
        $secondsToAdd = max($initialSecondsPerImage - $i, $minimumSecondsPerImage);
        $extraReadingTimeInSeconds += $secondsToAdd;
    }
    return $extraReadingTimeInSeconds;
}

function calculateReadingTime($title, $header, $content)
{
    $fullText = $title . ' ' . $header . ' ' . $content;
    $wordCount = str_word_count(strip_tags($fullText));
    $wordsPerMinute = 238;
    $readingTimeInMinutes = ceil($wordCount / $wordsPerMinute);

    $extraReadingTimeInSeconds = calculateExtraReadingTimeForImages($content);
    $extraReadingTimeInMinutes = ceil($extraReadingTimeInSeconds / 60);

    return $readingTimeInMinutes + $extraReadingTimeInMinutes;
}

function emptyTempUploadFolder()
{
    $folderPath = __DIR__ . '/temp_upload/';
    $files = glob($folderPath . '*');

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent']) && !empty($_POST['articleTitle']) && !empty($_POST['article_id'])) {
    $article_id = filter_var($_POST['article_id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var(trim($_POST['articleTitle']), FILTER_SANITIZE_STRING);
    $category_id = filter_var($_POST['category_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
    $content = $_POST['articleContent'];
    $header = filter_var($_POST['header'], FILTER_SANITIZE_STRING);
    $author_id = filter_var($_POST['author_id'], FILTER_SANITIZE_NUMBER_INT);
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $imageFile = null;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!in_array($logged_in_user_credential['level_name'], ['Admin', 'Editor', 'Author'])) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour mettre à jour un article.";
            header("Location: ../admin_post.php");
            exit;
        }

        $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
        $stmt->execute([$author_id]);
        $author = $stmt->fetchColumn();

        if (!$author) {
            throw new Exception("Author not found");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        exit;
    }

    if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
        $imageDir = "../images/";
        $timestamp = time();
        $sanitizedTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($title, 0, 50));
        $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
        $imageFile = $imageDir . "{$sanitizedTitle}_{$timestamp}.{$imageFileType}";
        $uploadOk = 1;

        $check = getimagesize($_FILES["mainImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["mainImage"]["size"] > 1000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk === 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (!move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFile)) {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    $totalReadingTimeInMinutes = calculateReadingTime($title, $header, $content);

    try {
        if ($imageFile) {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, image = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $imageFile, $totalReadingTimeInMinutes, $content, $header, $article_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $totalReadingTimeInMinutes, $content, $header, $article_id]);
        }

        if ($stmt->rowCount() > 0) {
            echo "Article updated successfully.";
            emptyTempUploadFolder();
            $pdo->commit();
            header("Location: ../admin_post.php");
            exit;
        } else {
            echo "No changes were made to the article.";
            header("Location: ../admin_post.php");
            exit;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error updating article: " . $e->getMessage();
    }

    $pdo = null;
} else {
    echo "Invalid request or missing data.";
}

ob_end_flush();
?>
