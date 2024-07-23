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
    $initialSecondsPerImage = 12 / 60;
    $minimumSecondsPerImage = 1 / 60;
    $extraReadingTimeInSeconds = 0;
    $imageCount = substr_count($content, '<img>');

    for ($i = 0; $i < $imageCount; $i++) {
        $secondsToAdd = max($initialSecondsPerImage - $i / 60, $minimumSecondsPerImage);
        $extraReadingTimeInSeconds += $secondsToAdd;
    }
    return $extraReadingTimeInSeconds;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent']) && !empty($_POST['articleTitle']) && !empty($_POST['article_id'])) {
    $article_id = $_POST['article_id'];
    $title = trim($_POST['articleTitle']);
    $category_id = $_POST['category_id'] ?? null;
    $content = $_POST['articleContent'];
    $header = $_POST['header'];
    $author_id = $_POST['author_id']; // Get the author ID from the form
    $date = $_POST['date'];
    $imageFile = null;

    // Fetch the author's name using the author_id
    try {
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
        $imageDir = "./images/"; // Ensure this directory exists and is writable
        $imageFile = $imageDir . basename($_FILES["mainImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["mainImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($imageFile)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
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

    $readingDurationInSeconds = ceil((str_word_count($content) / 265)); // words per minute to seconds
    $extraReadingTimeForImages = calculateExtraReadingTimeForImages($content);
    $totalReadingTimeInSeconds = $readingDurationInSeconds + $extraReadingTimeForImages;

    // Prepare SQL statement for update
    try {
        if ($imageFile) {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, image = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $imageFile, $totalReadingTimeInSeconds, $content, $header, $article_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, category_id = ?, duree_reading = ?, texte = ?, header = ? WHERE id = ?");
            $stmt->execute([$title, $author, $date, $category_id, $totalReadingTimeInSeconds, $content, $header, $article_id]);
        }

        if ($stmt->rowCount() > 0) {
            echo "Article updated successfully.";
            header("Location: ../admin_post.php");
        } else {

            header("Location: ../admin_post.php");
            echo "No changes were made to the article.";
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