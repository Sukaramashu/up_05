<?php
require_once '../../includes/header.php';
require_once '../../includes/auth.php';
require_admin();

$page_title = "Редактирование пользователя";
require_once '../../models/User.php';

$db = (new Database())->connect();
$user_model = new User($db);

// Получаем ID пользователя из URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем данные пользователя
$current_user = $user_model->getById($id);
if (!$current_user) {
    header("Location: index.php");
    exit();
}

// Обработка формы
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => trim($_POST['username']),
        'role' => trim($_POST['role']),
        'email' => trim($_POST['email']),
        'last_name' => trim($_POST['last_name']),
        'first_name' => trim($_POST['first_name']),
        'middle_name' => trim($_POST['middle_name']),
        'phone' => trim($_POST['phone']),
        'address' => trim($_POST['address'])
    ];

    // Валидация
    if (empty($data['username'])) {
        $errors['username'] = 'Логин обязателен';
    }
    
    if (empty($data['last_name'])) {
        $errors['last_name'] = 'Фамилия обязательна';
    }
    
    if (empty($data['first_name'])) {
        $errors['first_name'] = 'Имя обязательно';
    }

    if (empty($errors)) {
        // Обновляем пользователя
        $user_model->id = $id;
        $user_model->username = $data['username'];
        $user_model->role = $data['role'];
        $user_model->email = $data['email'];
        $user_model->last_name = $data['last_name'];
        $user_model->first_name = $data['first_name'];
        $user_model->middle_name = $data['middle_name'];
        $user_model->phone = $data['phone'];
        $user_model->address = $data['address'];
        
        if ($user_model->update()) {
            $_SESSION['message'] = 'Данные пользователя успешно обновлены';
            header("Location: view.php?id=$id");
            exit();
        } else {
            $errors['general'] = 'Ошибка при обновлении пользователя';
        }
    }
}
?>

<div class="content-header">
    <h1 class="content-title">Редактирование пользователя</h1>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-person"></i> <?= htmlspecialchars($current_user['last_name'] . ' ' . $current_user['first_name']) ?>
    </div>
    <div class="card-body">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= $errors['general'] ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Логин *</label>
                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                               id="username" name="username" value="<?= htmlspecialchars($data['username'] ?? $current_user['username']) ?>">
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= $errors['username'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Роль *</label>
                        <select class="form-select" id="role" name="role">
                            <option value="admin" <?= ($current_user['role'] === 'admin') ? 'selected' : '' ?>>Администратор</option>
                            <option value="teacher" <?= ($current_user['role'] === 'teacher') ? 'selected' : '' ?>>Преподаватель</option>
                            <option value="employee" <?= ($current_user['role'] === 'employee') ? 'selected' : '' ?>>Сотрудник</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($data['email'] ?? $current_user['email']) ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Фамилия *</label>
                        <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                               id="last_name" name="last_name" value="<?= htmlspecialchars($data['last_name'] ?? $current_user['last_name']) ?>">
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Имя *</label>
                        <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                               id="first_name" name="first_name" value="<?= htmlspecialchars($data['first_name'] ?? $current_user['first_name']) ?>">
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Отчество</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" 
                               value="<?= htmlspecialchars($data['middle_name'] ?? $current_user['middle_name']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($data['phone'] ?? $current_user['phone']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= htmlspecialchars($data['address'] ?? $current_user['address']) ?>">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>