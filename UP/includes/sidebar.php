<?php
require_once __DIR__ . '/auth.php';
?>
<div class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-brand">
        <img src="/assets/images/logo.png" alt="Логотип">
        <span>Учет оборудования</span>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="/index.php" class="sidebar-nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-house-door sidebar-nav-icon"></i> Главная
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/templates/equipment/index.php" class="sidebar-nav-link <?= strpos($_SERVER['PHP_SELF'], 'equipment') !== false ? 'active' : '' ?>">
                <i class="bi bi-pc-display sidebar-nav-icon"></i> Оборудование
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/templates/classrooms/index.php" class="sidebar-nav-link <?= strpos($_SERVER['PHP_SELF'], 'classrooms') !== false ? 'active' : '' ?>">
                <i class="bi bi-building sidebar-nav-icon"></i> Аудитории
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/templates/users/index.php" class="sidebar-nav-link <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>">
                <i class="bi bi-people sidebar-nav-icon"></i> Пользователи
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/templates/inventory/index.php" class="sidebar-nav-link <?= strpos($_SERVER['PHP_SELF'], 'inventory') !== false ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check sidebar-nav-icon"></i> Инвентаризация
            </a>
        </li>
        <?php if(is_logged_in() && $_SESSION['user_role'] === 'admin'): ?>
        <li class="sidebar-nav-item">
            <a href="/templates/reports/equipment-transfer.php" class="sidebar-nav-link <?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text sidebar-nav-icon"></i> Отчеты
            </a>
        </li>
        <?php endif; ?>
        <li class="sidebar-nav-item mt-auto">
            <a href="/profile.php" class="sidebar-nav-link">
                <i class="bi bi-person sidebar-nav-icon"></i> Профиль
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/logout.php" class="sidebar-nav-link">
                <i class="bi bi-box-arrow-right sidebar-nav-icon"></i> Выход
            </a>
        </li>
    </ul>
</div>