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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'header_admin.php'; ?>
<div class="container">
    <button class="toggle-sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="sidebar">
        <a href="index_admin.php" class="logo">Vultures</a>
        <ul class="nav-links">
            <li><a href="./index_admin.php" >Overview</a></li>
            <li><a href="./editor.php" >Add Article</a></li>
            <li><a href="#">Quickstart</a></li>
            <li><a href="#">Posts</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">Comments</a></li>
            <li><a href="#">Users</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
        <a href="./logout.php" class="logout-btn">Se déconnecter</a>
    </div>
    <div class="main-content">
        <div class="editor-container">
            <h1>Create Your Article</h1>
            <form action="processes/save_article.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="articleTitle" placeholder="Enter your captivating title here" required>
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
                    <label for="editor">Content</label>
                    <textarea id="editor" name="articleContent">Start writing your amazing article here!</textarea>
                </div>

                <button type="submit" class="button-category-publish">Publish Article</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('collapsed');
        });
    });
    // Add active class to current nav item
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove the preventDefault() call
            navItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Store the active link in localStorage
            localStorage.setItem('activeLink', this.getAttribute('href'));
        });
    });

    // Set active class based on current page URL or stored value
    function setActiveLink() {
        const currentPage = window.location.pathname;
        const storedActiveLink = localStorage.getItem('activeLink');

        navItems.forEach(item => {
            if (item.getAttribute('href') === currentPage || item.getAttribute('href') === storedActiveLink) {
                item.classList.add('active');
            }
        });
    }

    // Call setActiveLink when the page loads
    window.addEventListener('load', setActiveLink);
</script>


<script src="https://cdn.tiny.cloud/1/r0jevhd96d198uc5hif2msl0nr3r3g4k3hd8xbwgnecunv9z/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 500,
        menubar: false,
        statusbar: false,
    });
</script>
</body>
</html>