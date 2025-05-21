<?php
require_once '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_admin();

if (!is_admin()) {
    header("Location: /UP/templates/classrooms/index.php");
    exit();
}

$page_title = "Удаление аудитории";

require_once '../../models/Classroom.php';
require_once '../../models/Equipment.php';

$db = (new Database())->connect();
$classroom = new Classroom($db);
$equipment = new Equipment($db);

// Получаем ID аудитории из URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Проверяем, есть ли аудитория
$current_classroom = $classroom->getById($id);
if (!$current_classroom) {
    header("Location: /UP/templates/classrooms/index.php");
    exit();
}

// Проверяем, есть ли оборудование в аудитории
$equipment_count = $equipment->getCountByClassroom($id);
?>

<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($page_title) ?></h1>
    <a href="/UP/templates/classrooms/view.php?id=<?= $id ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle text-danger"></i> Подтверждение удаления
    </div>
    <div class="card-body">
        <?php if ($equipment_count > 0): ?>
            <div class="alert alert-warning">
                <strong>Внимание!</strong> В этой аудитории находится <?= $equipment_count ?> единиц оборудования. 
                При удалении аудитории все оборудование будет перемещено в "Не назначено".
            </div>
        <?php endif; ?>

        <p>Вы действительно хотите удалить аудиторию <strong><?= htmlspecialchars($current_classroom['name']) ?></strong>?</p>
        
        <form id="deleteClassroomForm">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Удалить
            </button>
            <a href="/UP/templates/classrooms/view.php?id=<?= $id ?>" class="btn btn-secondary">
                Отмена
            </a>
        </form>
    </div>
</div>

<script>
document.getElementById('deleteClassroomForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Подтверждение удаления
    if (!confirm('Вы уверены, что хотите удалить эту аудиторию?')) {
        return;
    }
    
    // Сбор данных формы
    const formData = new FormData(this);
    
    // Показываем индикатор загрузки
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Удаление...';
    submitBtn.disabled = true;
    
    // Отправка на сервер
    fetch('/UP/templates/classrooms/delete_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Успешное удаление
            if (data.redirect) {
                window.location.href = data.redirect;
            }
            // Можно показать toast-уведомление об успехе
        } else {
            // Ошибка
            alert(data.message || 'Произошла ошибка при удалении');
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при отправке запроса');
    })
    .finally(() => {
        // Восстанавливаем кнопку
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>