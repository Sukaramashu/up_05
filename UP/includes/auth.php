<?php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if(!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: /UP/login.php');
        exit();
    }
}

function require_admin() {
    require_login();
    if($_SESSION['user_role'] !== 'admin') {
        header('Location: /UP/index.php');
        exit();
    }
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_user_role() {
    return $_SESSION['user_role'] ?? null;
}
?>