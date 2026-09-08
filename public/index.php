<?php
require_once __DIR__ . '/../src/TaskManager.php';

// محاولة تحميل المهام
try {
    $manager = new TaskManager();
} catch (Exception $e) {
    die('❌ خطأ: ' . $e->getMessage());
}

// معالجة الإجراءات
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// معالجة الإضافة عبر POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $manager->addTask($title);
    }
    // إعادة التوجيه إلى نفس الصفحة
    header('Location: index.php');
    exit;
}

// معالجة إنهاء المهمة
if ($action === 'done' && isset($_GET['id'])) {
    $manager->markDone((int)$_GET['id']);
    header('Location: index.php');
    exit;
}

// معالجة حذف المهمة
if ($action === 'delete' && isset($_GET['id'])) {
    $manager->deleteTask((int)$_GET['id']);
    header('Location: index.php');
    exit;
}

// جلب الإحصائيات والمهام
$stats = $manager->getStatistics();
$tasks = $manager->listTasks();
$allTasks = $manager->listTasks(true);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 مدير المهام</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* تحسينات إضافية */
        .task-item a {
            text-decoration: none;
            display: inline-block;
        }
        .task-item a.btn-done {
            background: #48bb78;
            color: white;
            padding: 6px 15px;
            border-radius: 8px;
        }
        .task-item a.btn-done:hover {
            background: #38a169;
        }
        .task-item a.btn-delete {
            background: #fc8181;
            color: white;
            padding: 6px 15px;
            border-radius: 8px;
        }
        .task-item a.btn-delete:hover {
            background: #e53e3e;
        }
        .btn-view-all {
            background: #4a5568;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }
        .btn-view-all:hover {
            background: #2d3748;
        }
        .all-tasks {
            margin-top: 20px;
            display: none;
        }
        .all-tasks.show {
            display: block;
        }
        .task-item.done-task {
            opacity: 0.6;
            border-right-color: #48bb78;
        }
        .task-item.done-task .task-title {
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 مدير المهام اليومي</h1>
            <div class="stats">
                <span>📊 المجموع: <?= $stats['total'] ?></span>
                <span>✅ منتهية: <?= $stats['done'] ?></span>
                <span>⏳ معلقة: <?= $stats['pending'] ?></span>
            </div>
        </header>

        <!-- نموذج الإضافة -->
        <form method="POST" action="index.php" class="add-task">
            <input type="text" name="title" placeholder="أضف مهمة جديدة..." required autofocus>
            <button type="submit">➕ إضافة</button>
        </form>

        <!-- قائمة المهام -->
        <div id="taskList">
            <?php if (empty($tasks)): ?>
                <p style="text-align:center;color:#888;padding:30px;">
                    📭 لا توجد مهام معلقة
                </p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item" data-id="<?= $task['id'] ?>">
                        <span class="task-title"><?= htmlspecialchars($task['title']) ?></span>
                        <span class="task-date">📅 <?= $task['created_at'] ?></span>
                        <div class="task-actions">
                            <a href="?action=done&id=<?= $task['id'] ?>" class="btn-done" onclick="return confirm('هل تريد إنهاء هذه المهمة؟')">
                                ✅ إنهاء
                            </a>
                            <a href="?action=delete&id=<?= $task['id'] ?>" class="btn-delete" onclick="return confirm('⚠️ هل أنت متأكد من الحذف؟')">
                                🗑️ حذف
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- زر عرض كل المهام -->
        <button class="btn-view-all" onclick="toggleAllTasks()">
            📋 عرض كل المهام
        </button>

        <!-- عرض كل المهام (بما فيها المنتهية) -->
        <div id="allTasks" class="all-tasks">
            <?php if (!empty($allTasks)): ?>
                <?php foreach ($allTasks as $task): ?>
                    <div class="task-item <?= $task['done'] ? 'done-task' : '' ?>">
                        <span class="task-title"><?= htmlspecialchars($task['title']) ?></span>
                        <span class="task-date">📅 <?= $task['created_at'] ?></span>
                        <span><?= $task['done'] ? '✅ منتهية' : '⏳ معلقة' ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;color:#888;padding:20px;">📭 لا توجد مهام</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleAllTasks() {
            const container = document.getElementById('allTasks');
            const btn = event.target;
            
            if (container.classList.contains('show')) {
                container.classList.remove('show');
                btn.textContent = '📋 عرض كل المهام';
            } else {
                container.classList.add('show');
                btn.textContent = '🔽 إخفاء المهام';
            }
        }
    </script>
</body>
</html>