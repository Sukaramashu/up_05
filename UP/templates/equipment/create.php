<?php
require_once '../../includes/header.php';
// require_once '../../includes/auth.php';
// require_admin();

$page_title = "Добавить аудиторию";

require_once '../../models/Classroom.php';
require_once '../../models/User.php';

$db = (new Database())->connect();
$classroom = new Classroom($db);
$users = (new User($db))->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classroom->name = $_POST['name'];
    $classroom->short_name = $_POST['short_name'];
    $classroom->responsible_user_id = $_POST['responsible_user_id'] ?: null;
    $classroom->temp_responsible_user_id = $_POST['temp_responsible_user_id'] ?: null;

    if ($classroom->create()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Аудитория успешно добавлена'
        ];
        header('Location: index.php');
        exit();
    } else {
        $error = "Ошибка при добавлении аудитории";
    }
}
?>

<h1><?= htmlspecialchars($page_title) ?></h1>

<?php if (isset($error)) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="name">Название аудитории</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="short_name">Краткое название</label>
        <input type="text" id="short_name" name="short_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="responsible_user_id">Ответственный пользователь</label>
        <select id="responsible_user_id" name="responsible_user_id" class="form-control">
            <option value="">Выберите пользователя</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?= $user->id ?>"><?= htmlspecialchars($user->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="temp_responsible_user_id">Временный ответственный</label>
        <select id="temp_responsible_user_id" name="temp_responsible_user_id" class="form-control">
            <option value="">Выберите пользователя</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?= $user->id ?>"><?= htmlspecialchars($user->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Добавить</button>
    <a href="index.php" class="btn btn-secondary">Отмена</a>
</form>