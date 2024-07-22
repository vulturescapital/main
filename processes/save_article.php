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

// Ensure user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent']) && !empty($_POST['articleTitle'])) {
    $title = trim($_POST['articleTitle']);
    $category_id = $_POST['category_id'] ?? null;
    $content = $_POST['articleContent'];
    $author = $_SESSION['username']; // Assuming you have the username stored in the session
    $date = date('Y-m-d H:i:s');

    // Handle file upload
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
            // if everything is ok, try to upload file
        } else {
            if (!move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFile)) {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    } else {
        echo "No file was uploaded or there was an upload error.";
        $uploadOk = 0;
    }

    if ($uploadOk) {
        $readingDurationInSeconds = ceil((str_word_count($content) / 265)); // words per minute to seconds
        $extraReadingTimeForImages = calculateExtraReadingTimeForImages($content);
        $totalReadingTimeInSeconds = $readingDurationInSeconds + $extraReadingTimeForImages;

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO article (name, author, date, category_id, image, file, duree_reading, texte, header) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind and execute
        $stmt->execute([$title, $author, $date, $category_id, $imageFile, null, $totalReadingTimeInSeconds, $content, null]);
        header("Location: ../index_admin.php");
        echo "Article saved successfully.";
    }

    // Close connection
    $pdo = null;
}
$conn = null;
?>
