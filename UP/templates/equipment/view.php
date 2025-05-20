<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_login();

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /templates/equipment/index.php');
    exit();
}

$page_title = "Просмотр оборудования";
require_once __DIR__ . '/../../../models/Equipment.php';

$db = (new Database())->connect();
$equipment = new Equipment($db);
$equipment->getById($_GET['id']);

if(!$equipment->id) {
    header('Location: /templates/equipment/index.php');
    exit();
}
?>

<div class="content-header">
    <h1 class="content-title">Просмотр оборудования</h1>
    <div>
        <a href="/templates/equipment/index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
        <?php if(get_current_user_role() === 'admin'): ?>
        <a href="/templates/equipment/edit.php?id=<?= $equipment->id ?>" class="btn btn-primary ms-2">
            <i class="bi bi-pencil"></i> Редактировать
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Основная информация
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Наименование:</div>
                    <div class="col-md-8"><?= htmlspecialchars($equipment->name) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Инвентарный номер:</div>
                    <div class="col-md-8"><?= htmlspecialchars($equipment->inventory_number) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Аудитория:</div>
                    <div class="col-md-8"><?= htmlspecialchars($equipment->classroom_name ?? 'Не указана') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Ответственный:</div>
                    <div class="col-md-8">
                        <?= htmlspecialchars($equipment->responsible_last_name ?? '') ?> 
                        <?= htmlspecialchars($equipment->responsible_first_name ?? '') ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Стоимость:</div>
                    <div class="col-md-8"><?= $equipment->cost ? number_format($equipment->cost, 2, '.', ' ') . ' руб.' : 'Не указана' ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Направление:</div>
                    <div class="col-md-8"><?= htmlspecialchars($equipment->direction_name ?? 'Не указано') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Статус:</div>
                    <div class="col-md-8">
                        <span class="badge bg-<?= 
                            $equipment->status_name === 'На ремонте' ? 'warning' : 
                            ($equipment->status_name === 'Сломано' ? 'danger' : 'success') 
                        ?>">
                            <?= htmlspecialchars($equipment->status_name ?? 'Не указан') ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Модель:</div>
                    <div class="col-md-8"><?= htmlspecialchars($equipment->model_name ?? 'Не указана') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Комментарий:</div>
                    <div class="col-md-8"><?= $equipment->comments ? nl2br(htmlspecialchars($equipment->comments)) : 'Нет комментария' ?></div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> История перемещений
            </div>
            <div class="card-body">
                <?php 
                $history = $equipment->getMovementHistory();
                if($history->rowCount() > 0): 
                ?>
                    <div class="list-group">
                        <?php while($row = $history->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Перемещено из <?= htmlspecialchars($row['from_classroom_name'] ?? 'неизвестно') ?> в <?= htmlspecialchars($row['to_classroom_name']) ?></h6>
                                <small><?= date('d.m.Y H:i', strtotime($row['changed_at'])) ?></small>
                            </div>
                            <small class="text-muted">Пользователь: <?= htmlspecialchars($row['user_last_name']) ?> <?= htmlspecialchars($row['user_first_name']) ?></small>
                            <?php if($row['comments']): ?>
                            <p class="mb-0 mt-1"><small>Комментарий: <?= htmlspecialchars($row['comments']) ?></small></p>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Нет данных о перемещениях</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-image"></i> Фотография
            </div>
            <div class="card-body text-center">
                <?php if($equipment->photo_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $equipment->photo_path)): ?>
                    <img src="<?= $equipment->photo_path ?>" alt="Фото оборудования" class="img-fluid rounded" style="max-height: 300px;">
                <?php else: ?>
                    <div class="text-muted py-5">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                        <p class="mt-2">Фотография отсутствует</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-ethernet"></i> Сетевые настройки
            </div>
            <div class="card-body">
                <?php 
                $network = $equipment->getNetworkSettings();
                if($network->rowCount() > 0): 
                    $row = $network->fetch(PDO::FETCH_ASSOC);
                ?>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">IP адрес:</div>
                        <div class="col-8"><?= htmlspecialchars($row['ip_address']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Маска подсети:</div>
                        <div class="col-8"><?= htmlspecialchars($row['subnet_mask']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Шлюз:</div>
                        <div class="col-8"><?= htmlspecialchars($row['gateway'] ?? 'Не указан') ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">DNS серверы:</div>
                        <div class="col-8">
                            <?= htmlspecialchars($row['dns1'] ?? 'Не указан') ?>
                            <?= $row['dns2'] ? '<br>' . htmlspecialchars($row['dns2']) : '' ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Сетевые настройки отсутствуют</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-plug"></i> Расходные материалы
            </div>
            <div class="card-body">
                <?php 
                $consumables = $equipment->getConsumables();
                if($consumables->rowCount() > 0): 
                ?>
                    <div class="list-group list-group-flush">
                        <?php while($row = $consumables->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= htmlspecialchars($row['name']) ?></h6>
                                <span class="badge bg-primary rounded-pill"><?= $row['quantity'] ?> шт.</span>
                            </div>
                            <small class="text-muted">Тип: <?= htmlspecialchars($row['type_name']) ?></small>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Нет расходных материалов</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>