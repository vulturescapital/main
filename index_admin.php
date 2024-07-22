<?php include 'header_admin.php'; ?>

<div class="container">
    <aside class="sidebar">
        <ul>
            <li><a href="#">Vue d'ensemble</a></li>
            <li><a href="#">Articles</a></li>
            <li><a href="#">Commentaires</a></li>
            <li><a href="#">Statistiques</a></li>
            <li><a href="#">Paramètres</a></li>
        </ul>
    </aside>
    <main>
        <h1>Tableau de bord</h1>
        <div class="card">
            <h2>Démarrage rapide</h2>
            <p>Commencez à gérer votre blog en quelques minutes</p>
            <pre><code>
// Exemple de code pour ajouter un nouvel article
const newPost = {
    title: "Mon nouvel article",
    content: "Contenu de l'article...",
    author: "Admin"
};

blog.addPost(newPost);
                </code></pre>
        </div>
        <div class="card">
            <h2>Statistiques récentes</h2>
            <p>Vues: 1000 | Commentaires: 50 | Likes: 200</p>
        </div>
    </main>
</div>

<?php include 'footer.php'; ?>
