<?php
include 'header.php';
?>
<div class="container">
    <div class="newsletter-container">
        <div class="newsletter-header">JOIN OUR MAILING LIST</div>
        <div class="newsletter-title">
            Get the best stories from<br>
            the Vulture community.
        </div>
        <form class="newsletter-form" action="processes/add_email_process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="email" name="email" placeholder="Email Address" class="newsletter-input" required>
            <button type="submit" class="newsletter-submit">Submit</button>
        </form>
    </div>
</div>
