<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database configuration file
require_once 'dbconfig.php';

// Including the header part of your HTML page
include 'header.php';
?>

<div class="container">
    <img src="./images/logo_vultures.svg" alt="Under Construction Image" style="width:100%; height:auto;">-
    <div class="message">
        <p>Our website is currently under construction. Be the first to know when we launch by joining our mailing list.</p>
        <p>Notre site Web est actuellement en cours de construction. Soyez le premier à savoir quand nous lançons en rejoignant notre liste de diffusion.</p>
    </div>
    <hr class="horizontal-line">
    <div class="newsletter-container">
        <div class="newsletter-header">JOIN OUR MAILING LIST</div>
        <div class="newsletter-title">
            Don't miss our opening! Subscribe now!
        </div>
        <form class="newsletter-form" action="add_email_process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="email" name="email" placeholder="Email Address" class="newsletter-input" required>
            <button type="submit" class="newsletter-submit">Submit</button>
        </form>
    </div>
    <hr class="horizontal-line">
</div>