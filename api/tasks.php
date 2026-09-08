<?php
// تعيين الـ Headers الصحيحة
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// معالجة طلب OPTIONS (للـ CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../src/TaskManager.php';

// دالة لإرجاع خطأ JSON
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

try {
    $manager = new TaskManager();
} catch (Exception $e) {
    sendError('فشل تحميل المدير: ' . $e->getMessage(), 500);
}

// GET Request - جلب البيانات
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $action = $_GET['action'] ?? 'pending';
        
        if ($action === 'all') {
            $tasks = $manager->listTasks(true);
        } else {
            $tasks = $manager->listTasks(false);
        }
        
        // تحويل المصفوفة إلى JSON مع معالجة القيم غير المرئية
        echo json_encode(array_values($tasks));
    } catch (Exception $e) {
        sendError('فشل جلب البيانات: ' . $e->getMessage(), 500);
    }
    exit;
}

// POST Request - تعديل البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قراءة البيانات الخام
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        sendError('لا توجد بيانات مرسلة');
    }
    
    $data = json_decode($rawInput, true);
    
    if ($data === null) {
        sendError('بيانات JSON غير صالحة: ' . json_last_error_msg());
    }
    
    $action = $data['action'] ?? '';
    
    try {
        $result = false;
        $message = '';
        
        switch ($action) {
            case 'add':
                if (empty($data['title'])) {
                    sendError('عنوان المهمة مطلوب');
                }
                $task = $manager->addTask($data['title']);
                $result = ['success' => true, 'task' => $task];
                break;
                
            case 'done':
                if (empty($data['id'])) {
                    sendError('رقم المهمة مطلوب');
                }
                $success = $manager->markDone($data['id']);
                $result = ['success' => $success];
                if (!$success) {
                    $result['error'] = 'المهمة غير موجودة';
                }
                break;
                
            case 'delete':
                if (empty($data['id'])) {
                    sendError('رقم المهمة مطلوب');
                }
                $success = $manager->deleteTask($data['id']);
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

// أي طريقة أخرى
sendError('Method not allowed', 405);
?>