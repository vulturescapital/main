<?php
include 'dbconfig.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

function calculateExtraReadingTimeForImages($content) {
    $initialSecondsPerImage = 12;
    $minimumSecondsPerImage = 1;
    $extraReadingTimeInSeconds = 0;

    // Assuming images are included in the content with an <img> tag
    $imageCount = substr_count($content, '<img');

    for ($i = 0; $i < $imageCount; $i++) {
        $secondsToAdd = max($initialSecondsPerImage - $i, $minimumSecondsPerImage);
        $extraReadingTimeInSeconds += $secondsToAdd;
    }

    return $extraReadingTimeInSeconds;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['articleContent'])) {
    $category_id = $_POST['category_id'];
    // Calculate the reading duration
    $wordCount = str_word_count($_POST['articleContent']);
    $readingDurationInSeconds = ($wordCount / 265);

    // Now, $readingDurationInSeconds holds the duration value you'll insert into the database
    $extraReadingTimeForImages = calculateExtraReadingTimeForImages($_POST['articleContent']);
    $totalReadingTimeInSeconds = $readingDurationInSeconds + $extraReadingTimeForImages;
    $totalReadingTimeInSeconds = ceil($totalReadingTimeInSeconds);
    // Prepare and bind your INSERT statement to include the reading duration
    $stmt = $pdo->prepare("INSERT INTO article (name, author, date, category_id, image, file, duree_reading, texte, header) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Example data, replace with your actual form data
    $name = 'Article Title'; // You'll want to capture this from the form as well
    $author = $_SESSION['username']; // Example using a session variable
    $date = date('Y-m-d H:i:s'); // Current date and time
    $image = ''; // Assuming no images for now
    $file = ''; // Assuming no files for now
    $texte = $_POST['articleContent']; // The article content from CKEditor
    $header = ''; // Assuming header is empty for now

    // Execute the statement with the calculated total reading duration
    $stmt->execute([$name, $author, $date, $category_id, $image, $file, $totalReadingTimeInSeconds, $texte, $header]);
    echo "Article saved successfully.";
    // ... (Rest of your code)
   
}
else{
    echo "non";
}

// Close connection
$pdo = null;
?>
