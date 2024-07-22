<?php include 'header.php'; ?>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<div class="page-container">
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php
        if(isset($_SESSION['error'])) {
            echo '<p style="color: red;">'.$_SESSION['error'].'</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="processes/connect.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
