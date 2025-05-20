<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_login();

$page_title = "Аудитории";
require_once __DIR__ . '/../../../models/Classroom.php';

$db = (new Database())->connect();
$classroom = new Classroom($db);
$classrooms = $classroom->getWithEquipmentCount();
?>

<div class="content-header">
    <h1 class="content-title">Аудитории</h1>
    <div>
        <?php if(get_current_user_role() === 'admin'): ?>
        <a href="/templates/classrooms/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Добавить
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> Список аудиторий
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Наименование</th>
                        <th>Краткое название</th>
                        <th>Оборудование</th>
                        <th>Ответственный</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($classrooms->rowCount() > 0): ?>
                        <?php while($row = $classrooms->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['short_name']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= $row['equipment_count'] ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['responsible_last_name'] ?? '') ?> 
                                <?= htmlspecialchars($row['responsible_first_name'] ?? '') ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="/templates/classrooms/view.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if(get_current_user_role() === 'admin'): ?>
                                    <a href="/templates/classrooms/edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/templates/classrooms/delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Вы уверены?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Аудитории не найдены</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>