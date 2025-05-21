<?php
require_once  '../../includes/header.php';
// require_once  '../../includes/auth.php';
// require_admin();

$page_title = "Добавить пользователя";
require_once '../../models/User.php';

$db = (new Database())->connect();
$user = new User($db);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->username = trim($_POST['username']);
    $user->password = trim($_POST['password']);
    $user->role = $_POST['role'];
    $user->email = trim($_POST['email']);
    $user->last_name = trim($_POST['last_name']);
    $user->first_name = trim($_POST['first_name']);
    $user->middle_name = trim($_POST['middle_name']);
    $user->phone = trim($_POST['phone']);
    $user->address = trim($_POST['address']);

    // Валидация
    if (empty($user->username) || empty($user->password) || empty($user->last_name)) {
        $error = "Логин, пароль и фамилия обязательны для заполнения";
    } else {
        if ($user->create()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Пользователь успешно добавлен'
            ];
            header('Location: /templates/users/index.php');
            exit();
        } else {
            $error = "Ошибка при добавлении пользователя. Возможно, логин уже занят.";
        }
    }
}
?>

<div class="content-header">
    <h1 class="content-title">Добавить пользователя</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Назад
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus"></i> Новый пользователь
    </div>
    <div class="card-body">
        <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label required-field">Логин</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label required-field">Пароль</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label required-field">Роль</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin" <?= isset($_POST['role']) && $_POST['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                            <option value="teacher" <?= isset($_POST['role']) && $_POST['role'] === 'teacher' ? 'selected' : '' ?>>Преподаватель</option>
                            <option value="employee" <?= isset($_POST['role']) && $_POST['role'] === 'employee' ? 'selected' : '' ?>>Сотрудник</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label required-field">Фамилия</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Отчество</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" 
                               value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Адрес</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Сохранить
                </button>
                <a href="/UP/templates/users/index.php" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('.toggle-password').addEventListener('click', function() {
    const passwordInput = document.querySelector(this.getAttribute('toggle'));
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});
</script>

<?php require_once '../../includes/footer.php'; ?>