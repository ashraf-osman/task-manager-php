<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// معالجة OPTIONS (للـ CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// تضمين الملفات
require_once __DIR__ . '/../src/TaskManager.php';

// دالة لإرجاع خطأ
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

try {
    $manager = new TaskManager();
} catch (Exception $e) {
    sendError('فشل تحميل البيانات: ' . $e->getMessage(), 500);
}

// GET Request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $action = $_GET['action'] ?? 'pending';
        $tasks = ($action === 'all') ? $manager->listTasks(true) : $manager->listTasks(false);
        echo json_encode(array_values($tasks));
    } catch (Exception $e) {
        sendError('فشل جلب البيانات: ' . $e->getMessage(), 500);
    }
    exit;
}

// POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        sendError('لا توجد بيانات مرسلة');
    }
    
    $data = json_decode($rawInput, true);
    
    if ($data === null) {
        sendError('بيانات غير صالحة: ' . json_last_error_msg());
    }
    
    $action = $data['action'] ?? '';
    
    try {
        $result = false;
        
        switch ($action) {
            case 'add':
                if (empty($data['title'])) {
                    sendError('عنوان المهمة مطلوب');
                }
                $task = $manager->addTask($data['title']);
                $result = ['success' => true, 'task' => $task];
                break;
                
            case 'done':
                if (!isset($data['id'])) {
                    sendError('رقم المهمة مطلوب');
                }
                $success = $manager->markDone((int)$data['id']);
                $result = ['success' => $success];
                if (!$success) {
                    $result['error'] = 'المهمة غير موجودة';
                }
                break;
                
            case 'delete':
                if (!isset($data['id'])) {
                    sendError('رقم المهمة مطلوب');
                }
                $success = $manager->deleteTask((int)$data['id']);
                $result = ['success' => $success];
                if (!$success) {
                    $result['error'] = 'المهمة غير موجودة';
                }
                break;
                
            default:
                sendError('إجراء غير معروف: ' . $action);
        }
        
        echo json_encode($result);
    } catch (Exception $e) {
        sendError('خطأ في التنفيذ: ' . $e->getMessage(), 500);
    }
    exit;
}

sendError('Method not allowed', 405);
?>