<?php
define('SECURE_ACCESS', true);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin_login.php");
    exit;
}

try {
    // Fetch the logged-in user's credential level
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!in_array($logged_in_user_credential['level_name'], ['Admin', 'Editor', 'Author','Contributor'])) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour ajouter un article.";
        header("Location: ../index_admin.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $date = $_POST['date'];
        $category_id = intval($_POST['category_id']);
        $content = $_POST['content'];
        $header = $_POST['header'];

        // Image upload logic
        $uploadOk = 1;
        $imageDir = "../images/";
        $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
        $timestamp = time();
        $sanitizedTitle = preg_replace("/[^a-zA-Z0-9]/", "", $title);
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

        if ($uploadOk) {
            $totalReadingTimeInMinutes = calculateTotalReadingTime($title, $header, $content);
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
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la vérification des droits: " . $e->getMessage();
    header("Location: ../index_admin.php");
}
?>
