<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$title = 'Управление пользователями';
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

ob_start();
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Управление пользователями</h2>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Логин</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Роль</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['login']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td>
                                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-success' : 'bg-primary' ?>">
                                        <?= $user['role'] === 'admin' ? 'Админ' : 'Пользователь' ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="view_user.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary">Просмотр</a>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="make_admin.php?id=<?= $user['id'] ?>" class="btn btn-outline-success">Сделать админом</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../templates/footer.php';
?>