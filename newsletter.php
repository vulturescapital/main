<?php
include 'dbconfig.php';
include 'header.php';
?>
<div class="newsletter-outer">
    <h1 class="newsletter-title-page">NEWSLETTER</h1>
    <div class="newsletter-container">
        <div class="newsletter-header">JOIN OUR MAILING LIST</div>
        <div class="newsletter-title">
            Get the best stories from<br>
            the Sequoia community.
        </div>
        <form class="newsletter-form" action="add_email_process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="email" name="email" placeholder="Email Address" class="newsletter-input" required>
            <button type="submit" class="newsletter-submit">Submit</button>
        </form>
    </div>
</div>
