<?php
require_once '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../models/Equipment.php';
    require_once '../../models/Classroom.php';
    require_once '../../models/ReferenceItem.php';
    
    $db = (new Database())->connect();
    $equipment = new Equipment($db);
    
    $id = $_POST['id'];
    $data = [
        'name' => $_POST['name'],
        'inventory_number' => $_POST['inventory_number'],
        'classroom_id' => $_POST['classroom_id'],
        'status_id' => $_POST['status_id'],
        'description' => $_POST['description'] ?? null,
        'specifications' => $_POST['specifications'] ?? null
    ];
    
    if ($equipment->update($id, $data)) {
        $_SESSION['success'] = "Оборудование успешно обновлено";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Ошибка при обновлении оборудования";
    }
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    header("Location: index.php");
    exit();
}

$db = (new Database())->connect();
$equipment = new Equipment($db);
$item = $equipment->getById($id);

if (!$item) {
    header("Location: index.php");
    exit();
}

// Получаем статусы для выпадающего списка
$statuses = (new ReferenceItem($db))->getByType('status');
// Получаем аудитории для выпадающего списка
$classrooms = (new Classroom($db))->getAll();

$page_title = "Редактирование оборудования";
?>

<div class="content-header">
    <h1 class="content-title">Редактирование оборудования</h1>
    <div>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Наименование *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="inventory_number" class="form-label">Инвентарный номер *</label>
                <input type="text" class="form-control" id="inventory_number" name="inventory_number" 
                       value="<?= htmlspecialchars($item['inventory_number']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="classroom_id" class="form-label">Аудитория</label>
                <select class="form-select" id="classroom_id" name="classroom_id">
                    <option value="">Не указана</option>
                    <?php while($classroom = $classrooms->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $classroom['id'] ?>" 
                            <?= $item['classroom_id'] == $classroom['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($classroom['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="status_id" class="form-label">Статус *</label>
                <select class="form-select" id="status_id" name="status_id" required>
                    <?php while($status = $statuses->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $status['id'] ?>" 
                            <?= $item['status_id'] == $status['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="specifications" class="form-label">Характеристики</label>
                <textarea class="form-control" id="specifications" name="specifications" rows="3"><?= htmlspecialchars($item['specifications'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Сохранить
            </button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>