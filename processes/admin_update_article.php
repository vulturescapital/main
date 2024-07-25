<?php
session_start();
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
    // Combine title, header, and content for word count
    $fullText = $title . ' ' . $header . ' ' . $content;
    $wordCount = str_word_count(strip_tags($fullText));
    $wordsPerMinute = 238;
    $readingTimeInMinutes = ceil($wordCount / $wordsPerMinute);

    // Calculate extra time for images
    $extraReadingTimeInSeconds = calculateExtraReadingTimeForImages($content);
    $extraReadingTimeInMinutes = ceil($extraReadingTimeInSeconds / 60);

    return $readingTimeInMinutes + $extraReadingTimeInMinutes;
}

function emptyTempUploadFolder()
{
    $folderPath = __DIR__ . '/temp_upload/';
    $files = glob($folderPath . '*'); // get all file names

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // delete file
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent']) && !empty($_POST['articleTitle']) && !empty($_POST['article_id'])) {
    $article_id = $_POST['article_id'];
    $title = trim($_POST['articleTitle']);
    $category_id = $_POST['category_id'] ?? null;
    $content = $_POST['articleContent'];
    $header = $_POST['header'];
    $author_id = $_POST['author_id'];
    $date = $_POST['date'];
    $imageFile = null;

    // Fetch the author's name using the author_id
    try {
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
        echo "Error: " . $e->getMessage();
        exit;
    }

    // Handle file upload if a new image is provided
    if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
        $imageDir = "../images/"; // Ensure this directory exists and is writable
        $timestamp = time();
        $sanitizedTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($title, 0, 50));
        $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
        $imageFile = $imageDir . "{$sanitizedTitle}_{$timestamp}.{$imageFileType}";
        $uploadOk = 1;

        // Check if image file is an actual image
        $check = getimagesize($_FILES["mainImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file size is too large
        if ($_FILES["mainImage"]["size"] > 1000000) { // Size in bytes
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk === 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFile)) {
                // File uploaded successfully
            } else {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    $totalReadingTimeInMinutes = calculateReadingTime($title, $header, $content);

    // Prepare SQL statement for update
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
            header("Location: ../admin_post.php");
            exit;
        } else {
            echo "No changes were made to the article.";
            header("Location: ../admin_post.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error updating article: " . $e->getMessage();
    }

    // Close connection
    $pdo = null;
} else {
    echo "Invalid request or missing data.";
}
?>
