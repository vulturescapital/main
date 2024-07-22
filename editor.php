<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Include your database configuration file
include 'dbconfig.php';

$categories = [];
try {
    // Fetch the categories from the database
    $stmt = $pdo->query("SELECT id, name FROM category");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle errors and output a message
    die("Error retrieving categories: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Editor</title>
    <link rel="stylesheet" type="text/css" href="css/editor.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="editor-container">
    <h1>Write Your Article</h1>
    <form action="save_article.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="articleTitle" placeholder="Enter your article title here" required>
        </div>
        <label for="mainImage">Main Image:</label>
        <input type="file" name="mainImage" id="mainImage" required>
        <textarea id="editor" name="articleContent">Welcome to TinyMCE!</textarea>

        <label for="category">Category:</label>
        <select id="category" name="category_id">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" class="button-category-publish" value="Select the category & Publish">
    </form>
</div>

<!-- Include TinyMCE -->
<script src="https://cdn.tiny.cloud/1/huipd2b5w0f8r3sba5bztwu825k42jnezpkea3zvdt0sqtv2/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        // Define your TinyMCE configuration here
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',

        // ...other TinyMCE configurations
    });
</script>
</body>
</html>
