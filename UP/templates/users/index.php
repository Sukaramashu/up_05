<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_admin();

$page_title = "Пользователи";
require_once __DIR__ . '/../../../models/User.php';

$db = (new Database())->connect();
$user = new User($db);

// Пагинация и поиск
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$search = $_GET['search'] ?? '';

$total_users = $user->countAll($search);
$total_pages = ceil($total_users / $per_page);

// Получаем пользователей для текущей страницы
$users = $user->getPaginated($current_page, $per_page, $search);
?>

<div class="content-header">
    <h1 class="content-title">Пользователи</h1>
    <div>
        <a href="/templates/users/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Добавить
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-people"></i> Список пользователей
    </div>
    <div class="card-body">
        <div class="mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" placeholder="Поиск по имени или логину..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Найти
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="/templates/users/index.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Сброс
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Логин</th>
                        <th>ФИО</th>
                        <th>Роль</th>
                        <th>Email</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users->rowCount() > 0): ?>
                        <?php while($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['last_name']) ?> 
                                <?= htmlspecialchars($row['first_name']) ?>
                                <?= htmlspecialchars($row['middle_name']) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $row['role'] === 'admin' ? 'danger' : 
                                    ($row['role'] === 'teacher' ? 'primary' : 'secondary') 
                                ?>">
                                    <?= 
                                        $row['role'] === 'admin' ? 'Администратор' : 
                                        ($row['role'] === 'teacher' ? 'Преподаватель' : 'Сотрудник') 
                                    ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="/templates/users/view.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/templates/users/edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/templates/users/delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Вы уверены?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Пользователи не найдены</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Пагинация -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>