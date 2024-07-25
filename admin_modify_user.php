<?php
// Start output buffering
ob_start();
include 'header_admin.php';


// Check login status and redirect if necessary
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Check if the user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$user_id = $_GET['id'];

try {
    // Fetch the credential level of the logged-in user
    $stmt = $pdo->prepare("SELECT c.level_name FROM user u LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $logged_in_user_credential = $stmt->fetch(PDO::FETCH_ASSOC);
    // Fetch the user details to be modified
    $stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "Utilisateur non trouvé.";
        header("Location: admin_users.php");
        exit;
    }

    // Check if the logged-in user has the right credentials to modify a user
    if ($logged_in_user_credential != 'Admin' && $_SESSION['user_id'] != $user_id) {
        $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour modifier cet utilisateur.";
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
            <h1>Modifier un Utilisateur</h1>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form id="modifyUserForm" action="processes/process_modify_user.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="surname">Prénom</label>
                    <input type="text" class="form-control" id="surname" name="surname"
                           value="<?= htmlspecialchars($user['surname']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username"
                           value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" class="form-control" id="password" name="password">
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
                    <?php if ($logged_in_user_credential['level_name'] == 'Admin'): ?>
                        <select class="form-control" id="credential" name="credential_id" required>
                            <?php
                            // Fetch credential levels from the database
                            $stmt = $pdo->query("SELECT id, level_name FROM credentials");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value=\"" . htmlspecialchars($row['id']) . "\"" . ($row['id'] == $user['credential_id'] ? ' selected' : '') . ">" . htmlspecialchars($row['level_name']) . "</option>";
                            }
                            ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" name="credential_id"
                               value="<?= htmlspecialchars($user['credential_id']) ?>">
                        <p class="form-control-static"><span
                                    class="badge badge-info"><?= htmlspecialchars($logged_in_user_credential['level_name']) ?></span>
                        </p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Modifier</button>
            </form>
        </div>
    </div>

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
                criteria.length.querySelector('i').classList.toggle('fa-check', password.length >= 12);
                criteria.length.querySelector('i').classList.toggle('fa-times', password.length < 12);

                criteria.uppercase.classList.toggle('valid', /[A-Z]/.test(password));
                criteria.uppercase.classList.toggle('invalid', !/[A-Z]/.test(password));
                criteria.uppercase.querySelector('i').classList.toggle('fa-check', /[A-Z]/.test(password));
                criteria.uppercase.querySelector('i').classList.toggle('fa-times', !/[A-Z]/.test(password));

                criteria.lowercase.classList.toggle('valid', /[a-z]/.test(password));
                criteria.lowercase.classList.toggle('invalid', !/[a-z]/.test(password));
                criteria.lowercase.querySelector('i').classList.toggle('fa-check', /[a-z]/.test(password));
                criteria.lowercase.querySelector('i').classList.toggle('fa-times', !/[a-z]/.test(password));

                criteria.number.classList.toggle('valid', /\d/.test(password));
                criteria.number.classList.toggle('invalid', !/\d/.test(password));
                criteria.number.querySelector('i').classList.toggle('fa-check', /\d/.test(password));
                criteria.number.querySelector('i').classList.toggle('fa-times', !/\d/.test(password));

                criteria.special.classList.toggle('valid', /[!@#\$%\^\&*\)\(+=._-]/.test(password));
                criteria.special.classList.toggle('invalid', !/[!@#\$%\^\&*\)\(+=._-]/.test(password));
                criteria.special.querySelector('i').classList.toggle('fa-check', /[!@#\$%\^\&*\)\(+=._-]/.test(password));
                criteria.special.querySelector('i').classList.toggle('fa-times', !/[!@#\$%\^\&*\)\(+=._-]/.test(password));
            });

            document.getElementById('modifyUserForm').addEventListener('submit', function (e) {
                const password = passwordInput.value;
                if (password) {
                    const isValid = password.length >= 12 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password) && /[!@#\$%\^\&*\)\(+=._-]/.test(password);

                    if (!isValid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Le mot de passe ne respecte pas les règles de sécurité.'
                        });
                    }
                }
            });
        });
    </script>

<?php
ob_end_flush();
?>