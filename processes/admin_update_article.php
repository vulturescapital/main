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

function saveImageFromContent($base64Image, $articleDir, $title) {
    $sanitizedTitle = preg_replace("/[^a-zA-Z0-9]/", "", $title);
    $imageData = explode(',', $base64Image);
    $imageInfo = explode(';', $imageData[0]);
    $imageFileType = explode('/', $imageInfo[0])[1];
    $imageFileName = "{$sanitizedTitle}_" . uniqid() . ".{$imageFileType}";
    $imageFilePath = $articleDir . '/' . $imageFileName;

    if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        file_put_contents($imageFilePath, base64_decode($imageData[1]));
        return $imageFilePath;
    }
    return null;
}

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

        // Verify author and user credentials
        $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
        $stmt->execute([$author_id]);
        $author = $stmt->fetchColumn();

        if (!$author) {
            throw new Exception("Author not found");
        }

        // Create the article directory based on the title if it doesn't exist
        $sanitizedTitle = preg_replace("/[^a-zA-Z0-9]/", "", $title);
        $articleDir = "../articles/{$sanitizedTitle}";

        if (!is_dir($articleDir)) {
            mkdir($articleDir, 0777, true);
        }

        // Handle the main image upload
        if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
            $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
            $imageFileName = "featured_image.{$imageFileType}";
            $imageFilePath = $articleDir . '/' . $imageFileName;
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

            if ($uploadOk && !move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFilePath)) {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            } else {
                $relativeImagePath = $articleDir . '/' . $imageFileName;
            }
        }

        // Process images within content and save them to the article directory
        preg_match_all('/<img[^>]+src="data:image\/[^;]+;base64,[^"]+"/', $content, $matches);
        if (isset($matches[0])) {
            foreach ($matches[0] as $imgTag) {
                preg_match('/src="([^"]+)"/', $imgTag, $imgSrc);
                if (isset($imgSrc[1])) {
                    $newImagePath = saveImageFromContent($imgSrc[1], $articleDir, $title);
                    if ($newImagePath) {
                        $content = str_replace($imgSrc[1], $newImagePath, $content);
                    }
                }
            }
        }

        // Calculate total reading time
        $totalReadingTimeInMinutes = calculateReadingTime($title, $header, $content);

        // Update the article in the database
        if (isset($relativeImagePath)) {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, image = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $relativeImagePath, $totalReadingTimeInMinutes, $content, $header, $article_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $totalReadingTimeInMinutes, $content, $header, $article_id]);
        }

        // Commit and finalize
        if ($stmt->rowCount() > 0) {
            echo "Article updated successfully.";
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
