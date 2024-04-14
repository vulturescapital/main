<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Editor</title>
    <!-- Inclure CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
</head>
<body>
    <h1>Write Your Article</h1>
    <form action="save_article.php" method="post">
        <textarea id="editor" name="articleContent"></textarea>
        <input type="submit" value="Save Article">
    </form>

    <!-- Activer CKEditor sur le textarea -->
    <script>
        CKEDITOR.replace('editor');
    </script>
</body>
</html>
