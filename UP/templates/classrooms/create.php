<?php
require_once '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_admin();

$page_title = "Добавить аудиторию";

require_once '../../models/User.php';

$db = (new Database())->connect();
$users = (new User($db))->getAll();
?>

<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($page_title) ?></h1>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> Новая аудитория
    </div>
    <div class="card-body">
        <form id="createClassroomForm">
            <div class="mb-3">
                <label for="name" class="form-label">Название аудитории *</label>
                <input type="text" id="name" name="name" class="form-control" required>
                <div class="invalid-feedback" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label for="short_name" class="form-label">Краткое название *</label>
                <input type="text" id="short_name" name="short_name" class="form-control" required>
                <div class="invalid-feedback" id="shortNameError"></div>
            </div>

            <div class="mb-3">
                <label for="responsible_user_id" class="form-label">Ответственный пользователь</label>
                <select id="responsible_user_id" name="responsible_user_id" class="form-select">
                    <option value="">Выберите пользователя</option>
                    <?php while($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="temp_responsible_user_id" class="form-label">Временный ответственный</label>
                <select id="temp_responsible_user_id" name="temp_responsible_user_id" class="form-select">
                    <option value="">Выберите пользователя</option>
                    <?php 
                    $users->execute(); // Сброс курсора для повторного использования
                    while($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Сохранить
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createClassroomForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Сброс ошибок
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Сбор данных формы
    const formData = new FormData(this);
    
    // Отправка на сервер
    fetch('/UP/templates/classrooms/create_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Успешное создание
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            // Обработка ошибок
            if (data.errors) {
                for (const [field, message] of Object.entries(data.errors)) {
                    const input = document.getElementById(field);
                    const errorElement = document.getElementById(`${field}Error`);
                    
                    if (input && errorElement) {
                        input.classList.add('is-invalid');
                        errorElement.textContent = message;
                    }
                }
            }
            
            // Показать общее сообщение об ошибке
            if (data.message) {
                alert(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при отправке формы');
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>