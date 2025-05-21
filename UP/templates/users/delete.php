<?php
require_once '../../includes/header.php';
require_once '../../includes/auth.php';
require_admin();

require_once '../../models/User.php';
require_once '../../models/Equipment.php';
require_once '../../models/Classroom.php';

$db = (new Database())->connect();
$user = new User($db);
$equipment = new Equipment($db);
$classroom = new Classroom($db);

// Получаем ID пользователя из URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Проверяем, существует ли пользователь
$current_user = $user->getById($id);
if (!$current_user) {
    $_SESSION['error'] = 'Пользователь не найден';
    header("Location: index.php");
    exit();
}

// Проверяем, является ли пользователь текущим
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'Вы не можете удалить свой собственный аккаунт';
    header("Location: index.php");
    exit();
}

// Проверяем связи пользователя
$is_responsible_for_equipment = $equipment->countByResponsibleUser($id) > 0;
$is_responsible_for_classrooms = $classroom->countByResponsibleUser($id) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        try {
            // Начинаем транзакцию
            $db->beginTransaction();
            
            // Если пользователь ответственный - сбрасываем связи
            if ($is_responsible_for_equipment) {
                $equipment->clearResponsibleUser($id);
            }
            
            if ($is_responsible_for_classrooms) {
                $classroom->clearResponsibleUser($id);
            }
            
            // Удаляем пользователя
            if ($user->delete($id)) {
                $db->commit();
                $_SESSION['message'] = 'Пользователь успешно удален';
                header("Location: index.php");
                exit();
            }
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Ошибка при удалении пользователя: ' . $e->getMessage();
            header("Location: view.php?id=$id");
            exit();
        }
    } else {
        // Отмена удаления
        header("Location: view.php?id=$id");
        exit();
    }
}
?>

<div class="content-header">
    <h1 class="content-title">Удаление пользователя</h1>
    <a href="view.php?id=<?= $id ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle text-danger"></i> Подтверждение удаления
    </div>
    <div class="card-body">
        <?php if ($is_responsible_for_equipment || $is_responsible_for_classrooms): ?>
            <div class="alert alert-warning">
                <strong>Внимание!</strong> Этот пользователь является ответственным:
                <ul>
                    <?php if ($is_responsible_for_equipment): ?>
                        <li>За <?= $equipment->countByResponsibleUser($id) ?> единиц оборудования</li>
                    <?php endif; ?>
                    <?php if ($is_responsible_for_classrooms): ?>
                        <li>За <?= $classroom->countByResponsibleUser($id) ?> аудиторий</li>
                    <?php endif; ?>
                </ul>
                При удалении все связи будут сброшены.
            </div>
        <?php endif; ?>

        <p>Вы действительно хотите удалить пользователя <strong><?= htmlspecialchars($current_user['last_name'] . ' ' . $current_user['first_name']) ?></strong> (логин: <?= htmlspecialchars($current_user['username']) ?>)?</p>
        
        <form method="post">
            <button type="submit" name="confirm_delete" value="1" class="btn btn-danger">
                <i class="bi bi-trash"></i> Удалить
            </button>
            <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">
                Отмена
            </a>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>