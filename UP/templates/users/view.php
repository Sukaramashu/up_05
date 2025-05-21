<?php
require_once  '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_login();

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location:index.php');
    exit();
}

$page_title = "Просмотр пользователя";
require_once '../../models/User.php';

$db = (new Database())->connect();
$user = new User($db);
$user->getById($_GET['id']);

if(!$user->id) {
    header('Location: index.php');
    exit();
}

// Получаем закрепленное оборудование
$equipment = $user->getAssignedEquipment();
?>

<div class="content-header">
    <h1 class="content-title">Просмотр пользователя</h1>
    <div>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
        <?php if(get_current_user_role() === 'admin' || $_SESSION['user_id'] == $user->id): ?>
        <a href="/UP/templates/users/edit.php?id=<?= $user->id ?>" class="btn btn-primary ms-2">
            <i class="bi bi-pencil"></i> Редактировать
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge"></i> Основная информация
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Логин:</div>
                    <div class="col-md-8"><?= htmlspecialchars($user->username) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">ФИО:</div>
                    <div class="col-md-8">
                        <?= htmlspecialchars($user->last_name) ?> 
                        <?= htmlspecialchars($user->first_name) ?>
                        <?= htmlspecialchars($user->middle_name) ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Роль:</div>
                    <div class="col-md-8">
                        <span class="badge bg-<?= 
                            $user->role === 'admin' ? 'danger' : 
                            ($user->role === 'teacher' ? 'primary' : 'secondary') 
                        ?>">
                            <?= 
                                $user->role === 'admin' ? 'Администратор' : 
                                ($user->role === 'teacher' ? 'Преподаватель' : 'Сотрудник') 
                            ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Email:</div>
                    <div class="col-md-8"><?= htmlspecialchars($user->email) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Телефон:</div>
                    <div class="col-md-8"><?= htmlspecialchars($user->phone) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Адрес:</div>
                    <div class="col-md-8"><?= htmlspecialchars($user->address) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pc-display"></i> Закрепленное оборудование
            </div>
            <div class="card-body">
                <?php if($equipment->rowCount() > 0): ?>
                    <div class="list-group">
                        <?php while($row = $equipment->fetch(PDO::FETCH_ASSOC)): ?>
                        <a href="/UP/templates/equipment/view.php?id=<?= $row['id'] ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= htmlspecialchars($row['name']) ?></h6>
                                <small><?= htmlspecialchars($row['inventory_number']) ?></small>
                            </div>
                            <small class="text-muted">Статус: 
                                <span class="badge bg-<?= 
                                    $row['status_name'] === 'На ремонте' ? 'warning' : 
                                    ($row['status_name'] === 'Сломано' ? 'danger' : 'success') 
                                ?>">
                                    <?= htmlspecialchars($row['status_name']) ?>
                                </span>
                            </small>
                        </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Нет закрепленного оборудования</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>