<?php include 'header_admin.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}?>
<div class="container">
    <div class="sidebar">
        <a href="index_admin.php" class="logo">Vultures</a>
        <ul class="nav-links">
            <li><a href="#">Overview</a></li>
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
        <input type="text" class="search-bar" placeholder="Search posts...">
        <h1>Blog Admin Platform</h1>
        <div class="content-box">
            <h2>Quickstart</h2>
            <p>Create a new blog post in minutes</p>
            <div class="code-block">
                    <pre>
from blog_admin import BlogPost

post = BlogPost(
    title="My First Blog Post",
    content="Hello, world!",
    category="Technology"
)
post.publish()
                    </pre>
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
<script>
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