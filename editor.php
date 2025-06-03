<?php
define('SECURE_ACCESS', true);

include 'dbconfig.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1  .0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Create Your Article</title>
</head>
<body>
<?php include 'header_admin.php'; ?>
<div class="main-content">
    <div class="editor-container">
        <h1>Create Your Article</h1>
        <form action="processes/save_article.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="articleTitle" placeholder="Enter your captivating title here"
                       required>
            </div>
            <div class="form-group">
                <label for="postDate">Post Date</label>
                <input type="date" id="postDate" name="postDate" required>
            </div>
            <div class="form-group">
                <label for="mainImage">Featured Image</label>
                <input type="file" name="mainImage" id="mainImage" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="headerParagraph">Header Paragraph</label>
                <textarea id="headerParagraph" name="headerParagraph"
                          placeholder="Write a brief introduction for your article"></textarea>
            </div>

            <div class="form-group">
                <label for="editor">Content</label>
                <textarea id="editor" name="articleContent">Start writing your amazing article here!</textarea>
            </div>

            <!-- Hidden field to store the calculated reading time -->
            <input type="hidden" name="duree_reading" value="0">

            <button type="submit" class="button-category-publish">Publish Article</button>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/r0jevhd96d198uc5hif2msl0nr3r3g4k3hd8xbwgnecunv9z/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
<script>
    // Function to calculate reading time based on text and image content
    function calculateReadingTime(text) {
        const wordsPerMinute = 238;
        const wordCount = text.split(/\s+/).length;
        return Math.ceil(wordCount / wordsPerMinute);
    }

    function calculateImageTime(content) {
        const imageCount = (content.match(/<img/g) || []).length;
        const secondsPerImage = 12;
        return Math.ceil((imageCount * secondsPerImage) / 60);
    }

    function calculateTotalReadingTime() {
        const title = document.getElementById('title').value;
        const header = tinymce.get('headerParagraph').getContent({format: 'text'});
        const content = tinymce.get('editor').getContent({format: 'text'});

        const fullText = title + ' ' + header + ' ' + content;
        const textReadingTime = calculateReadingTime(fullText);
        const imageReadingTime = calculateImageTime(tinymce.get('editor').getContent());

        const totalReadingTime = textReadingTime + imageReadingTime;

        // Update the hidden input with the calculated reading time
        document.querySelector('input[name="duree_reading"]').value = totalReadingTime;
    }

    // Initialize TinyMCE editors and set up event listeners
    tinymce.init({
        selector: '#editor, #headerParagraph',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | addcomment showcomments | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 300,
        menubar: false,
        statusbar: false,
    });

    document.getElementById('title').addEventListener('input', calculateTotalReadingTime);

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', function () {
        calculateTotalReadingTime();
    });
</script>

</body>
</html>