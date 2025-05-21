<?php
require_once '../../includes/auth.php';
require_admin();

require_once '../../models/Classroom.php';
require_once '../../models/Equipment.php';

header('Content-Type: application/json');

$db = (new Database())->connect();
$classroom = new Classroom($db);
$equipment = new Equipment($db);

$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

// Получаем ID аудитории
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Проверяем, есть ли аудитория
$current_classroom = $classroom->getById($id);
if (!$current_classroom) {
    $response['message'] = 'Аудитория не найдена';
    $response['redirect'] = '/UP/templates/classrooms/index.php';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Начинаем транзакцию
        $db->beginTransaction();
        
        // Проверяем и перемещаем оборудование
        $equipment_count = $equipment->getCountByClassroom($id);
        if ($equipment_count > 0) {
            if (!$equipment->moveAllFromClassroom($id, null)) {
                throw new Exception('Ошибка перемещения оборудования');
            }
        }
        
        // Удаляем аудиторию
        if ($classroom->delete($id)) {
            $db->commit();
            $response['success'] = true;
            $response['message'] = 'Аудитория успешно удалена';
            $response['redirect'] = '/UP/templates/classrooms/index.php';
        } else {
            throw new Exception('Ошибка при удалении аудитории');
        }
    } catch (Exception $e) {
        $db->rollBack();
        $response['message'] = $e->getMessage();
        $response['redirect'] = "/UP/templates/classrooms/view.php?id=$id";
    }
} else {
    $response['message'] = 'Некорректный метод запроса';
}

echo json_encode($response);