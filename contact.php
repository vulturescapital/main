<?php
include 'header.php'; ?>
<div id="popup" class="popup"></div>
<div id="notification" class="notification"></div>
<div class="container-main-contact">
    <div class="contact-container">
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form action="processes/contact_process.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="input-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="input-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="5" required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</div>
<script>
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
    if (isset($_GET['success'])) {
        $status_message = 'Message sent successfully!';
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
