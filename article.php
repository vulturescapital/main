<?php
include 'header.php';
$article = [];
$related_articles = [];

// Validate the ID and fetch article details.
if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    // Prepare and execute the statement to select the article.
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id = :id");
    $stmt->execute([':id' => $article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($article) {
        if (empty($_SESSION['viewed_articles']) || !in_array($article_id, $_SESSION['viewed_articles'])) {
            // Increment the views count for the article
            $update_stmt = $pdo->prepare("UPDATE article SET views = views + 1 WHERE id = :id");
            $update_stmt->execute([':id' => $article_id]);

            // Store article ID in session to prevent repeated counts during the session
            $_SESSION['viewed_articles'][] = $article_id;
        }

        // Fetch the text content of all other articles
        $stmt = $pdo->prepare("SELECT id, texte FROM article WHERE id != :id");
        $stmt->execute([':id' => $article_id]);
        $allOtherArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Levenshtein distances
        $levenshteinDistances = [];
        foreach ($allOtherArticles as $otherArticle) {
            $levDist = levenshtein($article['texte'], $otherArticle['texte']);
            $levenshteinDistances[$otherArticle['id']] = $levDist;
        }

        // Sort articles by their Levenshtein distance (smallest distance first)
        asort($levenshteinDistances);

        // Get the IDs of the four most related articles
        $closestMatchesIds = array_keys(array_slice($levenshteinDistances, 0, 4, true));

        // Fetch the related articles with the closest match
        $inQuery = implode(',', array_fill(0, count($closestMatchesIds), '?'));
        $related_articles_stmt = $pdo->prepare("SELECT * FROM article WHERE id IN ($inQuery)");
        $related_articles_stmt->execute($closestMatchesIds);
        $related_articles = $related_articles_stmt->fetchAll(PDO::FETCH_ASSOC);

        $most_viewed_stmt = $pdo->prepare("SELECT * FROM article WHERE id != :id ORDER BY views DESC LIMIT 5");
        $most_viewed_stmt->execute([':id' => $article_id]);
        $most_viewed_articles = $most_viewed_stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        header('Location: errorpage.php'); // Redirect to an error page if no article is found.
        exit;
    }
} else {
    header('Location: errorpage.php'); // Redirect to an error page if ID is invalid.
    exit;
}
?>
<div id="popup" class="popup"></div>
<div id="notification" class="notification"></div>
<div class="container">
    <!-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="articles_choices.php">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">AI is Now Shovel Ready</li>
        </ol>
    </nav>-->
    <hr class="horizontal-line">
    <div class="article-header">
        <h1 class="article-title"><?= htmlspecialchars($article['name']); ?></h1>
        <h2 class="article-subtitle"><?= htmlspecialchars($article['header']); ?></h2>
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
        <img src="<?= htmlspecialchars($article['image']); ?>" alt="<?= htmlspecialchars($article['name']); ?>"
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
    <script>
        function getCleanUrl() {
            const url = new URL(window.location.href);
            url.search = ''; // Remove query parameters
            url.hash = ''; // Remove hash
            return url.href;
        }

        function shareOnFacebook() {
            const url = getCleanUrl();
            const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            window.open(facebookUrl, '_blank');
        }

        function shareOnTwitter() {
            const url = getCleanUrl();
            const text = document.querySelector('.article-title').innerText;
            const twitterUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
            window.open(twitterUrl, '_blank');
        }

        function shareOnLinkedIn() {
            const url = getCleanUrl();
            const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            window.open(linkedInUrl, '_blank');
        }

        function shareViaEmail() {
            const url = getCleanUrl();
            const subject = document.querySelector('.article-title').innerText;
            const body = `Check out this article: ${url}`;
            const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoUrl;
        }

        function copyLink() {
            const url = getCleanUrl();
            navigator.clipboard.writeText(url).then(() => {
            }).catch(err => {
            });
        }

        // Function to show pop-up message
        function showPopup(message, type) {
            const popup = document.getElementById("popup");
            popup.innerText = message;
            popup.classList.add("show");
            popup.classList.add(type);
            setTimeout(() => {
                popup.classList.remove("show");
                popup.classList.remove(type);
            }, 3000);
        }

        // Function to get query parameters
        function getQueryParams() {
            const params = {};
            window.location.search.substring(1).split("&").forEach(pair => {
                const [key, value] = pair.split("=");
                params[key] = decodeURIComponent(value);
            });
            return params;
        }

        // Display appropriate message based on query parameter
        const queryParams = getQueryParams();
        if (queryParams.success) {
            showPopup("Subscription successful!", "success");
        } else if (queryParams.error) {
            let message;
            switch (queryParams.error) {
                case "invalid_email":
                    message = "Invalid email address!";
                    break;
                case "email_exists":
                    message = "Email is already subscribed!";
                    break;
                case "db_error":
                    message = "Database error occurred!";
                    break;
                case "invalid_csrf":
                    message = "Invalid CSRF token!";
                    break;
                default:
                    message = "An unknown error occurred!";
            }
            showPopup(message, "error");
        }
    </script>
    <div class="buy-me-a-coffee-container">
        <a href="https://www.buymeacoffee.com/vultures_capital"><img
                    src="https://img.buymeacoffee.com/button-api/?text=Buy me a coffee&emoji=☕&slug=vultures_capital&button_colour=5F7FFF&font_colour=ffffff&font_family=Lato&outline_colour=000000&coffee_colour=FFDD00"/></a>
    </div>
    <hr class="horizontal-line">
    <div class="related-articles-container">
        <h3>Articles Similaires</h3>
        <div class="related-container mt-4">
            <div class="related-row">
                <?php foreach ($related_articles as $related_article): ?>
                    <div class="related-col mb-4">
                        <a href="article.php?id=<?= htmlspecialchars($related_article['id']); ?>"
                           class="related-article-link">
                            <div class="related-card">
                                <img class="related-img-top" src="<?= htmlspecialchars($related_article['image']); ?>"
                                     alt="Article Image">
                                <div class="related-card-body">
                                    <h5 class="related-title"><?= htmlspecialchars($related_article['name']); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="newsletter-container">
        <div class="newsletter-header">JOIN OUR MAILING LIST</div>
        <div class="newsletter-title">
            Get the best stories from<br>
            the Vulture community.
        </div>
        <form class="newsletter-form" action="add_email_process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="email" name="email" placeholder="Email Address" class="newsletter-input" required>
            <button type="submit" class="newsletter-submit">Submit</button>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('notification');
        const statusMessage = <?php echo json_encode($status_message); ?>;

        if (statusMessage) {
            notification.textContent = statusMessage;
            notification.classList.add(statusMessage.includes('successfully') ? 'success' : 'error');
            notification.style.display = 'block';
        }
    });
</script>
<?php include 'footer.php'; ?>
