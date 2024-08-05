<?php
define('SECURE_ACCESS', true);

include "dbconfig.php";
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));
$image = '';
if (!empty($_FILES['mainImage']['name']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
    // This is a newly uploaded image
    $temp_dir = 'temp_uploads/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    $temp_path = $temp_dir . basename($_FILES['mainImage']['name']);
    if (move_uploaded_file($_FILES['mainImage']['tmp_name'], $temp_path)) {
        $image = $temp_path;
    }
} elseif (!empty($_POST['current_image'])) {
    $image = $_POST['current_image'];
} else {
    $image = 'default_image.jpg'; // Fallback to a default image if needed
}

// Use POST data instead of database query
$article = [
    'id' => $_POST['article_id'],
    'name' => $_POST['articleTitle'],
    'author' => $_POST['author_id'],
    'date' => $_POST['date'],
    'image' => $image,
    'duree_reading' => $_POST['duree_reading'],
    'header' => $_POST['header'],
    'texte' => $_POST['articleContent'],
    'category_id' => $_POST['category_id']
];
error_log("Selected image: " . $image);

// Fetch author name if needed
$stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
$stmt->execute([$article['author']]);
$article['author'] = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Preview</title>
    <style>
        :root {
            --background-color-light: #fbf7f0;
            --text-color-light: #333333;
            --text-color-light-bis: #003300;
        }

        body {
            font-family: Unica, BlinkMacSystemFont, Segoe UI, Helvetica Neue, Arial, FZLanTingHeiS, sans-serif;
            line-height: 1.6;
            color: var(--text-color-light);
            background-color: var(--background-color-light);
        }

        .horizontal-line {
            border-top: 0.5px solid #333;
            margin-bottom: 20px;
        }

        .article-header {
            text-align: center;
            max-width: 950px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .article-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .article-subtitle {
            font-size: 2rem;
            font-weight: normal;
            margin-bottom: 30px;
        }

        .article-author, .article-date, .article-reading-time {
            font-size: 0.9rem;
            color: #666;
        }

        .article-image-container {
            margin-bottom: 2rem;
        }

        .article-image {
            max-width: 66.5%;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
        }

        .article-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .article-content section.article-content {
            position: relative;
            padding: 0 40px;
            font-size: 1.1rem;
            line-height: 1.8;
            text-align: justify;
        }

        .article-content section.article-content::before,
        .article-content section.article-content::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 0.5px;
            background-color: #333;
        }

        .article-content section.article-content::before {
            left: 0;
        }

        .article-content section.article-content::after {
            right: 0;
        }

        .share-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .share-container h2 {
            font-size: 20px;
            margin-bottom: 15px;
            margin-top: 20px;
            font-weight: 100;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .share-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 18px;
            transition: background-color 0.3s, color 0.3s;
        }

        .share-button:hover {
            background-color: #000;
            color: #fff;
        }

        .share-button svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .share-link {
            margin-left: 15px;
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .share-link:hover {
            background-color: #333;
        }

        .buy-me-a-coffee-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 50px;
        }

        @media (max-width: 768px) {
            .article-content section.article-content {
                padding: 0 20px;
            }

            .article-content section.article-content::before,
            .article-content section.article-content::after {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container">
        <hr class="horizontal-line">
        <div class="article-header">
            <h1 class="article-title"><?= htmlspecialchars($article['name']); ?></h1>
            <h2 class="article-subtitle"><?= $article['header']; ?></h2>
            <p class="article-author">By <?= htmlspecialchars($article['author']); ?></p>
            <?php
            $date = new DateTime($article['date']);
            $formattedDate = strtoupper($date->format('F j, Y'));
            ?>
            <p class="article-date">Published <?= htmlspecialchars($formattedDate); ?></p>
            <p class="article-reading-time"><?= htmlspecialchars($article['duree_reading']); ?> min read</p>
        </div>
        <hr class="horizontal-line">
        <figure class="article-image-container">
            <img src="<?php echo htmlspecialchars($article['image']); ?>"
                 alt="<?php echo htmlspecialchars($article['name']); ?>"
                 class="article-image">
        </figure>
        <div class="article-content">
            <section class="article-content">
                <?= $article['texte']; ?>
            </section>
        </div>
        <div class="share-container">
            <h2>SHARE</h2>
            <div class="share-buttons">
                <a href="#" class="share-button" title="Share on Facebook" onclick="shareOnFacebook(); return false;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                    </svg>
                </a>
                <a href="#" class="share-button" title="Share on Twitter" onclick="shareOnTwitter(); return false;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a href="#" class="share-button" title="Share on LinkedIn" onclick="shareOnLinkedIn(); return false;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <a href="#" class="share-button" title="Share via Email" onclick="shareViaEmail(); return false;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </a>
                <button class="share-link" onclick="copyLink()">Copy Link</button>
            </div>
        </div>
        <div class="buy-me-a-coffee-container">
            <a href="https://www.buymeacoffee.com/vultures_capital"><img
                        src="https://img.buymeacoffee.com/button-api/?text=Buy me a coffee&emoji=☕&slug=vultures_capital&button_colour=5F7FFF&font_colour=ffffff&font_family=Lato&outline_colour=000000&coffee_colour=FFDD00"/></a>
        </div>
        <hr class="horizontal-line">
    </div>
</div>
</body>
</html>