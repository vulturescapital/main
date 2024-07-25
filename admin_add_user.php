<?php
ob_start(); // Start output buffering
include 'header_admin.php';


// Check login status and redirect if necessary
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Check user credentials
try {
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($logged_in_user_credential['level_name'] != 'Admin') {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour ajouter un utilisateur.";
        header("Location: admin_users.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la vérification des droits: " . $e->getMessage();
    header("Location: admin_users.php");
    exit;
}

?>

    <div class="container">
        <div class="main-content">
            <h1>Ajouter un Utilisateur</h1>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form id="addUserForm" action="processes/process_add_user.php" method="POST">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="surname">Prénom</label>
                    <input type="text" class="form-control" id="surname" name="surname" required>
                </div>
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div id="password-criteria" class="mt-2">
                        <p id="length" class="invalid"><i class="fas fa-times"></i> Au moins 12 caractères</p>
                        <p id="uppercase" class="invalid"><i class="fas fa-times"></i> Au moins une lettre majuscule</p>
                        <p id="lowercase" class="invalid"><i class="fas fa-times"></i> Au moins une lettre minuscule</p>
                        <p id="number" class="invalid"><i class="fas fa-times"></i> Au moins un chiffre</p>
                        <p id="special" class="invalid"><i class="fas fa-times"></i> Au moins un caractère spécial</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="credential">Niveau de Crédentiel</label>
                    <select class="form-control" id="credential" name="credential_id" required>
                        <?php
                        // Fetch credential levels from the database
                        $stmt = $pdo->query("SELECT id, level_name FROM credentials");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['level_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const criteria = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                lowercase: document.getElementById('lowercase'),
                number: document.getElementById('number'),
                special: document.getElementById('special')
            };

            passwordInput.addEventListener('input', function () {
                const password = passwordInput.value;
                criteria.length.classList.toggle('valid', password.length >= 12);
                criteria.length.classList.toggle('invalid', password.length < 12);
                criteria.uppercase.classList.toggle('valid', /[A-Z]/.test(password));
                criteria.uppercase.classList.toggle('invalid', !/[A-Z]/.test(password));
                criteria.lowercase.classList.toggle('valid', /[a-z]/.test(password));
                criteria.lowercase.classList.toggle('invalid', !/[a-z]/.test(password));
                criteria.number.classList.toggle('valid', /\d/.test(password));
                criteria.number.classList.toggle('invalid', !/\d/.test(password));
                criteria.special.classList.toggle('valid', /[!@#\$%\^\&*\)\(+=._-]/.test(password));
                criteria.special.classList.toggle('invalid', !/[!@#\$%\^\&*\)\(+=._-]/.test(password));
            });

            document.getElementById('addUserForm').addEventListener('submit', function (e) {
                const password = passwordInput.value;
                const isValid = password.length >= 12 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password) && /[!@#\$%\^\&*\)\(+=._-]/.test(password);

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Le mot de passe ne respecte pas les règles de sécurité.'
                    });
                }
            });
        });
    </script>

<?php
ob_end_flush(); // End output buffering and send output
?>