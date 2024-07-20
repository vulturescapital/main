<?php
include 'header.php';?>
<div class="container">
    <h1>Contact Us</h1>
    <form action="contact_process.php" method="POST" class="contact-form">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <i class="fas fa-user"></i>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <i class="fas fa-envelope"></i>
        </div>

        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
            <i class="fas fa-tag"></i>
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            <i class="fas fa-comment"></i>
        </div>

        <button type="submit">Send Message</button>
    </form>
</div>
<?php include 'footer.php'; ?>
