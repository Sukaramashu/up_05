<?php
require_once '../../includes/auth.php';
require_admin();

require_once '../../models/Classroom.php';
require_once '../../models/User.php';

header('Content-Type: application/json');

$db = (new Database())->connect();
$classroom = new Classroom($db);

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация данных
    $errors = [];
    
    if (empty($_POST['name'])) {
        $errors['name'] = 'Название аудитории обязательно';
    }
    
    if (empty($_POST['short_name'])) {
        $errors['short_name'] = 'Краткое название обязательно';
    }
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Пожалуйста, исправьте ошибки';
        echo json_encode($response);
        exit;
    }

    // Подготовка данных
    $classroom->name = trim($_POST['name']);
    $classroom->short_name = trim($_POST['short_name']);
    $classroom->responsible_user_id = !empty($_POST['responsible_user_id']) ? (int)$_POST['responsible_user_id'] : null;
    $classroom->temp_responsible_user_id = !empty($_POST['temp_responsible_user_id']) ? (int)$_POST['temp_responsible_user_id'] : null;

    // Создание аудитории
    if ($classroom->create()) {
        $response['success'] = true;
        $response['message'] = 'Аудитория успешно добавлена';
        $response['redirect'] = '/UP/templates/classrooms/index.php';
    } else {
        $response['message'] = 'Ошибка при добавлении аудитории';
    }
} else {
    $response['message'] = 'Некорректный метод запроса';
}

echo json_encode($response);