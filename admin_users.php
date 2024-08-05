<?php
define('SECURE_ACCESS', true);

include 'header_admin.php'; ?>
<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

try {
    // Fetch all users and their credential levels from the database
    $stmt = $pdo->query("
        SELECT u.id, u.name, u.surname, u.email, u.created_at, c.level_name 
        FROM user u
        LEFT JOIN credentials c ON u.credential_id = c.id WHERE u.credential_id!='5'
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If there's an error, return it as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?>

<div class="main-content">
    <h1>Liste des Utilisateurs</h1>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date de Création</th>
            <th>Niveau admin</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['surname']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                <td><?= htmlspecialchars($user['level_name']) ?></td>
                <td>
                    <a href="admin_modify_user.php?id=<?= $user['id'] ?>" class="modify-btn">Modifier</a>
                    <a href="processes/admin_delete_user.php?id=<?= $user['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '<?= htmlspecialchars($_SESSION['success']) ?>'
        });
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '<?= htmlspecialchars($_SESSION['error']) ?>'
        });
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
</script>
