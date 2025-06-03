<?php
define('SECURE_ACCESS', true);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
require_once 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin_login.php");
    exit;
}

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

try {
    // Fetch the logged-in user's credential level
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!in_array($logged_in_user_credential['level_name'], ['Admin', 'Editor', 'Author', 'Contributor'])) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour ajouter un article.";
        header("Location: ../index_admin.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve data from the form
        $title = isset($_POST['articleTitle']) ? trim($_POST['articleTitle']) : null;
        $author = $_SESSION['username']; // Automatically set the author based on the logged-in user
        $date = isset($_POST['postDate']) ? $_POST['postDate'] : date('Y-m-d');
        $category_id = intval($_POST['category_id']);
        $content = isset($_POST['articleContent']) ? trim($_POST['articleContent']) : '';
        $header = isset($_POST['headerParagraph']) ? trim($_POST['headerParagraph']) : '';
        $duree_reading = intval($_POST['duree_reading']);  // Reading time from the hidden field

        // Ensure the title is not null or empty
        if (empty($title)) {
            die("Title is required and cannot be empty.");
        }

        // Create a directory for this article using the title
        $sanitizedTitle = preg_replace("/[^a-zA-Z0-9]/", "", $title);
        $articleDir = "../articles/{$sanitizedTitle}";

        if (!is_dir($articleDir)) {
            mkdir($articleDir, 0777, true);
        }

        // Image upload logic for the featured image
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($_FILES["mainImage"]["name"], PATHINFO_EXTENSION));
        $imageFileName = "featured_image.{$imageFileType}";
        $imageFilePath = $articleDir . '/' . $imageFileName;

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
            if (!move_uploaded_file($_FILES["mainImage"]["tmp_name"], $imageFilePath)) {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }

        if ($uploadOk) {
            $relativeImagePath = $articleDir . '/' . $imageFileName;

            // Process images within content
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

            // Prepare SQL statement to insert the article into the database
            $stmt = $pdo->prepare("INSERT INTO article (name, author, date, category_id, image, file, duree_reading, texte, header) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Bind and execute
            $stmt->execute([$title, $author, $date, $category_id, $relativeImagePath, null, $duree_reading, $content, $header]);
            header("Location: ../index_admin.php");
            echo "Article saved successfully.";
        }

        // Close connection
        $pdo = null;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la vérification des droits: " . $e->getMessage();
    header("Location: ../index_admin.php");
}
?>
