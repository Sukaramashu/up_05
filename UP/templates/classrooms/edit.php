<?php
require_once '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_login();

if (!is_admin()) {
    header("Location: /UP/templates/classrooms/index.php");
    exit();
}

$page_title = "Редактирование аудитории";
require_once '../../models/Classroom.php';
require_once '../../models/User.php';

$db = (new Database())->connect();
$classroom = new Classroom($db);
$user = new User($db);

// Получаем ID аудитории из URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем данные аудитории
$current_classroom = $classroom->getById($id);
if (!$current_classroom) {
    header("Location: /UP/templates/classrooms/index.php");
    exit();
}

// Получаем список пользователей для выпадающего списка
$users = $user->getAll();

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'short_name' => trim($_POST['short_name']),
        'responsible_user_id' => !empty($_POST['responsible_user_id']) ? (int)$_POST['responsible_user_id'] : null,
        'temp_responsible_user_id' => !empty($_POST['temp_responsible_user_id']) ? (int)$_POST['temp_responsible_user_id'] : null
    ];

    // Валидация
    $errors = [];
    if (empty($data['name'])) {
        $errors['name'] = 'Название аудитории обязательно';
    }

    if (empty($errors)) {
        if ($classroom->update($id, $data)) {
            $_SESSION['message'] = 'Аудитория успешно обновлена';
            header("Location: /UP/templates/classrooms/view.php?id=$id");
            exit();
        } else {
            $errors['general'] = 'Ошибка при обновлении аудитории';
        }
    }
}
?>

<div class="content-header">
    <h1 class="content-title">Редактирование аудитории</h1>
    <a href="/UP/templates/classrooms/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> <?= htmlspecialchars($current_classroom['name']) ?>
    </div>
    <div class="card-body">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= $errors['general'] ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Название аудитории *</label>
                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= htmlspecialchars($data['name'] ?? $current_classroom['name']) ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="short_name" class="form-label">Краткое название</label>
                <input type="text" class="form-control" id="short_name" name="short_name" 
                       value="<?= htmlspecialchars($data['short_name'] ?? $current_classroom['short_name']) ?>">
            </div>

            <div class="mb-3">
                <label for="responsible_user_id" class="form-label">Ответственный</label>
                <select class="form-select" id="responsible_user_id" name="responsible_user_id">
                    <option value="">-- Не назначен --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" 
                            <?= ($user['id'] == ($data['responsible_user_id'] ?? $current_classroom['responsible_user_id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="temp_responsible_user_id" class="form-label">Временный ответственный</label>
                <select class="form-select" id="temp_responsible_user_id" name="temp_responsible_user_id">
                    <option value="">-- Не назначен --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" 
                            <?= ($user['id'] == ($data['temp_responsible_user_id'] ?? $current_classroom['temp_responsible_user_id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>