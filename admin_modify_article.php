<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Include your database configuration file
include 'dbconfig.php';

// Check if article ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid article ID");
}

$article_id = $_GET['id'];

// Fetch the article data
try {
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        die("Article not found");
    }

    // Fetch categories
    $stmt = $pdo->query("SELECT id, name FROM category");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users (authors)
    $stmt = $pdo->query("SELECT id, username FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <?php
    // ... (Keep the existing PHP code at the top)
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            .button-container {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }

            .button {
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                border: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }

            .button-preview {
                background-color: #4CAF50;
                color: white;
            }

            .button-preview:hover {
                background-color: #45a049;
            }

            .button-update {
                background-color: #008CBA;
                color: white;
            }

            .button-update:hover {
                background-color: #007B9E;
            }
        </style>
    </head>
    <body>
    <?php include 'header_admin.php'; ?>
    <div class="main-content">
        <div class="editor-container">
            <h1>Edit Article</h1>
            <form id="articleForm" action="processes/admin_update_article.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="article_id" value="<?= htmlspecialchars($article['id']) ?>">

                <div class="form-group">
                    <label for="title">Title</label>
                    <p>Current Title: <?= htmlspecialchars($article['name']) ?></p>
                    <input type="text" id="title" name="articleTitle" value="<?= htmlspecialchars($article['name']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Featured Image</label>
                    <p>Current image: <?= htmlspecialchars($article['image']) ?></p>
                    <input type="file" name="mainImage" id="mainImage">
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <p>Current Category: <?= htmlspecialchars($article['category_id']) ?></p>
                    <select id="category" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $article['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="author">Author</label>
                    <p>Current author(s): <?= htmlspecialchars($article['author']) ?></p>
                    <select id="author" name="author_id" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>" <?= $user['username'] == $article['author'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Publication Date</label>
                    <p>Current Date: <?= htmlspecialchars($article['date']) ?></p>
                    <input type="datetime-local" id="date" name="date"
                           value="<?= date('Y-m-d\TH:i', strtotime($article['date'])) ?>" required>
                </div>

                <div class="form-group">
                    <label for="duree_reading">Reading Duration (minutes)
                        : <?= htmlspecialchars($article['duree_reading']) ?></label>
                </div>

                <div class="form-group">
                    <label for="header">Header</label>
                    <textarea id="header" name="header"><?= htmlspecialchars($article['header']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="editor">Content</label>
                    <textarea id="editor" name="articleContent"><?= htmlspecialchars($article['texte']) ?></textarea>
                </div>

                <div class="button-container">
                    <button type="button" class="button button-preview" onclick="submitForm('preview')">Preview
                        Article
                    </button>
                    <button type="submit" class="button button-update">Update Article</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.tiny.cloud/1/r0jevhd96d198uc5hif2msl0nr3r3g4k3hd8xbwgnecunv9z/tinymce/7/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#editor, #header',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 500,
            menubar: false,
            statusbar: false,
        });

        function submitForm(action) {
            var form = document.getElementById('articleForm');

            // Ensure TinyMCE content is updated
            tinymce.triggerSave();

            if (action === 'preview') {
                // Change form action to preview page
                form.action = 'admin_preview_articles.php';
                form.target = '_blank'; // Open in a new tab
            }

            form.submit();
        }
    </script>

    </body>
    </html>