#!/usr/bin/env php
<?php
require_once __DIR__ . '/../src/TaskManager.php';

function showMenu() {
    echo "\n📋 مدير المهام - CLI\n";
    echo "═══════════════════════\n";
    echo "1. عرض المهام\n";
    echo "2. إضافة مهمة جديدة\n";
    echo "3. إنهاء مهمة\n";
    echo "4. حذف مهمة\n";
    echo "5. إحصائيات\n";
    echo "6. عرض كل المهام (بما فيها المنتهية)\n";
    echo "0. خروج\n";
    echo "اختر: ";
}

$manager = new TaskManager();

while (true) {
    showMenu();
    $choice = trim(fgets(STDIN));
    
    switch ($choice) {
        case '1':
            echo "\n📌 المهام غير المنتهية:\n";
            foreach ($manager->listTasks() as $task) {
                echo "[{$task['id']}] {$task['title']} (منذ {$task['created_at']})\n";
            }
            break;
            
        case '2':
            echo "عنوان المهمة: ";
            $title = trim(fgets(STDIN));
            if (!empty($title)) {
                $task = $manager->addTask($title);
                echo "✅ تمت الإضافة بنجاح (ID: {$task['id']})\n";
            }
            break;
            
        case '3':
            echo "رقم المهمة: ";
            $id = (int)trim(fgets(STDIN));
            if ($manager->markDone($id)) {
                echo "✅ تم إنهاء المهمة\n";
            } else {
                echo "❌ المهمة غير موجودة\n";
            }
            break;
            
        case '4':
            echo "رقم المهمة: ";
            $id = (int)trim(fgets(STDIN));
            if ($manager->deleteTask($id)) {
                echo "✅ تم حذف المهمة\n";
            } else {
                echo "❌ المهمة غير موجودة\n";
            }
            break;
            
        case '5':
            $stats = $manager->getStatistics();
            echo "\n📊 الإحصائيات:\n";
            echo "المجموع: {$stats['total']}\n";
            echo "منتهية: {$stats['done']}\n";
            echo "معلقة: {$stats['pending']}\n";
            break;
            
        case '6':
            echo "\n📌 جميع المهام:\n";
            foreach ($manager->listTasks(true) as $task) {
                $status = $task['done'] ? '✅' : '⏳';
                echo "[{$task['id']}] {$status} {$task['title']}\n";
            }
            break;
            
        case '0':
            echo "👋 مع السلامة!\n";
            exit(0);
            
        default:
            echo "❌ خيار غير صحيح\n";
    }
}
?>