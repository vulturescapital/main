<?php
include 'header_admin.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo "Article ID not provided.";
    exit;
}

try {
    // Fetch the article data
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        echo "Article not found.";
        exit;
    }

    // Fetch all categories
    $categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all users
    $users = $pdo->query("SELECT id, username FROM user")->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $author = $_POST['author'];
        $date = $_POST['date'];
        $image = $_POST['image'];
        $duree_reading = $_POST['duree_reading'];
        $texte = $_POST['texte'];
        $header = $_POST['header'];
        $category_id = $_POST['category_id'];

        // File upload handling
        $file = $article['file']; // Keep existing file by default
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                $file = $uploadFile;
            }
        }

        // Update the article
        $updateStmt = $pdo->prepare("UPDATE article SET name = ?, author = ?, date = ?, image = ?, file = ?, duree_reading = ?, texte = ?, header = ?, category_id = ? WHERE id = ?");
        $updateStmt->execute([$name, $author, $date, $image, $file, $duree_reading, $texte, $header, $category_id, $id]);

        echo "<p class='success-message'>Article updated successfully!</p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Article</title>
    <style>

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 600;
        }

        input[type="text"],
        input[type="file"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
        }

        input[type="text"]:focus,
        input[type="file"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .button-category-publish {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            margin-top: 2rem;
        }

        .button-category-publish:hover {
            background-color: var(--secondary-color);
        }

        .success-message {
            background-color: var(--secondary-color);
            color: white;
            padding: 1rem;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
    <script src="https://cdn.tiny.cloud/1/r0jevhd96d198uc5hif2msl0nr3r3g4k3hd8xbwgnecunv9z/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#header, #texte',
            height: 300,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            width: '100%',
            resize: false,
            elementpath: false,
            branding: false
        });
    </script>
</head>
<body>
<div class="main-content">
    <h1>Modify Article</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Title</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $article['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <select id="author" name="author" required>
                <option value="">Select an author</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['username'] ?>" <?= $user['username'] == $article['author'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($article['date']) ?>" required>
        </div>

        <div class="form-group">
            <label for="duree_reading">Reading Time (minutes)</label>
            <input type="number" id="duree_reading" name="duree_reading"
                   value="<?= htmlspecialchars($article['duree_reading']) ?>" required>
        </div>

        <div class="form-group">
            <label for="image">Image URL</label>
            <input type="text" id="image" name="image" value="<?= htmlspecialchars($article['image']) ?>">
        </div>

        <div class="form-group">
            <label for="file">File</label>
            <input type="file" id="file" name="file">
            <?php if ($article['file']): ?>
                <p>Current file: <?= htmlspecialchars($article['file']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label

                    for="header">Header</label>
            <textarea id="header" name="header"><?= htmlspecialchars($article['header']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="texte">Content</label>
            <textarea id="texte" name="texte"><?= htmlspecialchars($article['texte']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Views</label>
            <input type="text" value="<?= htmlspecialchars($article['views']) ?>" readonly>
        </div>

        <button type="submit" class="button-category-publish">Update Article</button>
    </form>
</div>

<?php include 'footer_admin.php'; ?>
</body>
</html>