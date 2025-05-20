<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_login();

$page_title = "Оборудование";
require_once __DIR__ . '/../../../models/Equipment.php';
require_once __DIR__ . '/../../../models/Classroom.php';
require_once __DIR__ . '/../../../models/ReferenceItem.php';

$db = (new Database())->connect();
$equipment = new Equipment($db);

// Поиск и фильтрация
$search = $_GET['search'] ?? '';
$status_id = $_GET['status_id'] ?? '';
$classroom_id = $_GET['classroom_id'] ?? '';

$stmt = $equipment->getAll($search, $status_id, $classroom_id);

// Получаем статусы для фильтра
$statuses = (new ReferenceItem($db))->getByType('status');
// Получаем аудитории для фильтра
$classrooms = (new Classroom($db))->getAll();
?>

<div class="content-header">
    <h1 class="content-title">Оборудование</h1>
    <div>
        <?php if(get_current_user_role() === 'admin'): ?>
        <a href="/templates/equipment/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Добавить
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Список оборудования
    </div>
    <div class="card-body">
        <div class="mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Поиск..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status_id">
                        <option value="">Все статусы</option>
                        <?php while($status = $statuses->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $status['id'] ?>" <?= $status_id == $status['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="classroom_id">
                        <option value="">Все аудитории</option>
                        <?php while($classroom = $classrooms->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $classroom['id'] ?>" <?= $classroom_id == $classroom['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($classroom['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Фильтр
                    </button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Наименование</th>
                        <th>Инв. номер</th>
                        <th>Аудитория</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($stmt->rowCount() > 0): ?>
                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['inventory_number']) ?></td>
                            <td><?= htmlspecialchars($row['classroom_name'] ?? 'Не указана') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $row['status_name'] === 'На ремонте' ? 'warning' : 
                                    ($row['status_name'] === 'Сломано' ? 'danger' : 'success') 
                                ?>">
                                    <?= htmlspecialchars($row['status_name'] ?? 'Не указан') ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="/templates/equipment/view.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if(get_current_user_role() === 'admin'): ?>
                                    <a href="/templates/equipment/edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary" title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/templates/equipment/delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" title="Удалить" onclick="return confirm('Вы уверены?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Оборудование не найдено</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>