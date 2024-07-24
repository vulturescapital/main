<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

function calculateExtraReadingTimeForImages($content) {
    $secondsPerImage = 5; // seconds per image
    $imageCount = substr_count($content, '<img');
    return $imageCount * $secondsPerImage;
}

function calculateReadingTime($title, $header, $content) {
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent']) && !empty($_POST['articleTitle'])) {
    $title = trim($_POST['articleTitle']);
    $category_id = $_POST['category_id'] ?? null;
    $content = $_POST['articleContent'];
    $header = $_POST['headerParagraph'] ?? '';
    $author = $_SESSION['username'];
    $date = $_POST['postDate'];
    $imageFile = null;

    // Handle file upload
    if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] == 0) {
        $imageDir = __DIR__ . "/../images/"; // Ensure this directory exists and is writable
        // Create the images directory if it doesn't exist
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        // Sanitize the title to create a valid file name
        $sanitizedTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($title, 0, 50));
        $timestamp = time();
        $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
        $imageFileName = "{$sanitizedTitle}_{$timestamp}.{$imageFileType}";
        $imageFilePath = $imageDir . $imageFileName;

        $uploadOk = 1;

        // Check if image file is an actual image
        $check = getimagesize($_FILES["mainImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
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
            if (!move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFilePath)) {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    } else {
        echo "No file was uploaded or there was an upload error.";
        $uploadOk = 0;
    }

    if ($uploadOk) {
        $totalReadingTimeInMinutes = calculateReadingTime($title, $header, $content);
        $relativeImagePath = '../images/' . $imageFileName;

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO article (name, author, date, category_id, image, file, duree_reading, texte, header) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind and execute
        $stmt->execute([$title, $author, $date, $category_id, $relativeImagePath, null, $totalReadingTimeInMinutes, $content, $header]);
        header("Location: ../index_admin.php");
        echo "Article saved successfully.";
    }

    // Close connection
    $pdo = null;
}
$conn = null;
?>
