<?php include 'header_admin.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}?>
    <div class="main-content">
        <input type="text" class="search-bar" placeholder="Search posts...">
        <h1>Blog Admin Platform</h1>
        <div class="content-box">
            <h2>Quickstart</h2>
            <p>Create a new blog post in minutes</p>
            <div class="code-block">
            </div>
        </div>
        <h2>Manage Your Content</h2>
        <div class="model-grid">
            <div class="model-card">
                <h3>Posts</h3>
                <p>Create, edit, and manage your blog posts</p>
            </div>
            <div class="model-card">
                <h3>Comments</h3>
                <p>Moderate and respond to user comments</p>
            </div>
            <div class="model-card">
                <h3>Categories</h3>
                <p>Organize your content with custom categories</p>
            </div>
            <div class="model-card">
                <h3>Users</h3>
                <p>Manage user accounts and permissions</p>
            </div>
        </div>
    </div>
</div>